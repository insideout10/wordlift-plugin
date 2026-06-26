$ErrorActionPreference = 'Stop'

# Ensure SlikSVN is in the PATH (VSCode sometimes caches old PATH variables after a fresh install)
if (!(Get-Command "svn" -ErrorAction SilentlyContinue)) {
    if (Test-Path "C:\Program Files\SlikSvn\bin\svn.exe") {
        $env:PATH += ";C:\Program Files\SlikSvn\bin"
    } else {
        Write-Host "SVN is not installed or not in PATH! Please install it first." -ForegroundColor Red
        exit 1
    }
}

$File = 'src\wordlift.php'
$Readme = 'trunk\readme.txt'

Write-Host "Checking out and updating the svn branch..." -ForegroundColor Cyan

# Create branch if it doesn't exist, otherwise just checkout
if (!(git rev-parse --verify svn 2>$null)) {
    git checkout -b svn | Out-Null
} else {
    git checkout svn | Out-Null
}

git pull origin svn 2>$null | Out-Null

Write-Host "Updating the svn branch..." -ForegroundColor Cyan
svn up

Write-Host "Checking out updated src..." -ForegroundColor Cyan
if (Test-Path "src") {
    Remove-Item -Recurse -Force "src"
}

git checkout main | Out-Null
git pull --all | Out-Null
git checkout svn | Out-Null
git checkout main -- src | Out-Null

# Extract version from wordlift.php
$content = Get-Content $File -Raw
if ($content -match "Version:\s+(?<version>\d+\.\d+\.\d+)") {
    $Version = $Matches['version']
}

Write-Host "Detected Version: $Version" -ForegroundColor Green

if ([string]::IsNullOrWhiteSpace($Version)) {
    Write-Host "Version not set, halting." -ForegroundColor Red
    exit 1
}

Write-Host "Removing tag $Version (if exists)..." -ForegroundColor Cyan
svn rm --force "tags/$Version" 2>$null | Out-Null

Write-Host "If you see 'forbidden by the server', you need to authenticate to the server first." -ForegroundColor Yellow
svn ci -m "${Version}: updating trunk (1 of 2)"

# Sync src to trunk (Robocopy returns 1 if files were copied, 0 if nothing changed, >8 for errors)
Write-Host "Syncing src/ to trunk/..." -ForegroundColor Cyan
$robo = robocopy "src" "trunk" /MIR /NFL /NDL /NJH /NJS /nc /ns /np
if ($LASTEXITCODE -ge 8) {
    Write-Host "Robocopy failed with exit code $LASTEXITCODE" -ForegroundColor Red
    exit 1
}

Write-Host "Setting the stable tag in $Readme..." -ForegroundColor Cyan
(Get-Content $Readme) -replace "Stable tag: .*", "Stable tag: $Version" | Set-Content $Readme

Write-Host "Syncing SVN status additions and deletions..." -ForegroundColor Cyan
# Add untracked files (?)
$untracked = svn status trunk | Where-Object { $_ -match "^\?\s+(.*)" } | ForEach-Object { $Matches[1] }
if ($untracked) {
    $untracked | ForEach-Object { svn add $_ }
}

# Delete missing files (!)
$deleted = svn status trunk | Where-Object { $_ -match "^!\s+(.*)" } | ForEach-Object { $Matches[1] }
if ($deleted) {
    $deleted | ForEach-Object { svn delete $_ }
}

svn cp trunk "tags/$Version" 2>$null | Out-Null

Write-Host "If you see 'forbidden by the server', you need to authenticate to the server first." -ForegroundColor Yellow
svn ci -m "${Version}: updating trunk (2 of 2)"

Write-Host "Removing src..." -ForegroundColor Cyan
Remove-Item -Recurse -Force "src"

git add -A
git commit -m "bump to $Version" -a
git push origin svn

Write-Host "Release completed successfully!" -ForegroundColor Green
