<?php

namespace Composer;

use Composer\Semver\VersionParser;

class InstalledVersions {

	private static $installed = array(
		'root'     =>
		array(
			'pretty_version' => 'dev-develop',
			'version'        => 'dev-develop',
			'aliases'        =>
			array(),
			'reference'      => '2b5035defcc45dcd4370a5865ba4fe5e91035435',
			'name'           => '__root__',
		),
		'versions' =>
		array(
			'__root__'                                 =>
			array(
				'pretty_version' => 'dev-develop',
				'version'        => 'dev-develop',
				'aliases'        =>
				array(),
				'reference'      => '2b5035defcc45dcd4370a5865ba4fe5e91035435',
			),
			'deliciousbrains/wp-background-processing' =>
			array(
				'pretty_version' => '1.0.2',
				'version'        => '1.0.2.0',
				'aliases'        =>
				array(),
				'reference'      => '2cbee1abd1b49e1133cd8f611df4d4fc5a8b9800',
			),
		),
	);

	public static function getInstalledPackages() {
		return array_keys( self::$installed['versions'] );
	}

	public static function isInstalled( $packageName ) {
		return isset( self::$installed['versions'][ $packageName ] );
	}

	public static function satisfies( VersionParser $parser, $packageName, $constraint ) {
		$constraint = $parser->parseConstraints( $constraint );
		$provided   = $parser->parseConstraints( self::getVersionRanges( $packageName ) );

		return $provided->matches( $constraint );
	}

	public static function getVersionRanges( $packageName ) {
		if ( ! isset( self::$installed['versions'][ $packageName ] ) ) {
			throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
		}

		$ranges = array();
		if ( isset( self::$installed['versions'][ $packageName ]['pretty_version'] ) ) {
			$ranges[] = self::$installed['versions'][ $packageName ]['pretty_version'];
		}
		if ( array_key_exists( 'aliases', self::$installed['versions'][ $packageName ] ) ) {
			$ranges = array_merge( $ranges, self::$installed['versions'][ $packageName ]['aliases'] );
		}
		if ( array_key_exists( 'replaced', self::$installed['versions'][ $packageName ] ) ) {
			$ranges = array_merge( $ranges, self::$installed['versions'][ $packageName ]['replaced'] );
		}
		if ( array_key_exists( 'provided', self::$installed['versions'][ $packageName ] ) ) {
			$ranges = array_merge( $ranges, self::$installed['versions'][ $packageName ]['provided'] );
		}

		return implode( ' || ', $ranges );
	}

	public static function getVersion( $packageName ) {
		if ( ! isset( self::$installed['versions'][ $packageName ] ) ) {
			throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
		}

		if ( ! isset( self::$installed['versions'][ $packageName ]['version'] ) ) {
			return null;
		}

		return self::$installed['versions'][ $packageName ]['version'];
	}

	public static function getPrettyVersion( $packageName ) {
		if ( ! isset( self::$installed['versions'][ $packageName ] ) ) {
			throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
		}

		if ( ! isset( self::$installed['versions'][ $packageName ]['pretty_version'] ) ) {
			return null;
		}

		return self::$installed['versions'][ $packageName ]['pretty_version'];
	}

	public static function getReference( $packageName ) {
		if ( ! isset( self::$installed['versions'][ $packageName ] ) ) {
			throw new \OutOfBoundsException( 'Package "' . $packageName . '" is not installed' );
		}

		if ( ! isset( self::$installed['versions'][ $packageName ]['reference'] ) ) {
			return null;
		}

		return self::$installed['versions'][ $packageName ]['reference'];
	}

	public static function getRootPackage() {
		return self::$installed['root'];
	}

	public static function getRawData() {
		return self::$installed;
	}

	public static function reload( $data ) {
		self::$installed = $data;
	}
}
