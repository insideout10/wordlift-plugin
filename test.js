var webdriver = require( 'selenium-webdriver' ),
	By = webdriver.By,
	until = webdriver.until;

var driver = new webdriver.Builder()
	.usingServer( 'http://localhost:4445' )
	.withCapabilities(
		{
			browserName: 'safari',
			platform: 'macOS 10.12',
			version: '10.0',
			username: 'ziodave',
			accessKey: '35a270f8-1742-4dc1-ab14-5e7bb1cc9042',
			'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER
		} ).build();

var driver = new webdriver.Builder()
	.forBrowser( 'safari' )
	.build();

//browser.setValue( '#user_login', 'admin' );
//browser.setValue( '#user_pass', 'admin' );
//browser.click( '#wp-submit' );

driver.get( 'http://wordpress.localhost/wp-login.php' );
driver.findElement( By.id( 'user_login' ) ).sendKeys( 'admin' );
driver.sleep( 1000 );
driver.findElement( By.id( 'user_pass' ) ).sendKeys( 'admin' );
driver.sleep( 1000 );
driver.findElement( By.id( 'wp-submit' ) ).click();
driver.wait( until.elementLocated( By.className( 'wp-admin' ) ), 10000 );

driver.findElement( By.css( '#menu-posts > a[href="edit.php"]' ) ).click();
driver.wait( until.elementLocated( By.css( 'a.page-title-action' ) ), 10000 );
driver.findElement( By.css( 'a.page-title-action' ) ).click();
driver.wait( until.elementLocated( By.id( 'content_ifr' ) ), 10000 );
driver.sleep( 5000 );
driver.switchTo().frame( 'content_ifr' );
driver.findElement( By.id( 'tinymce' ) ).click();
driver.findElement( By.id( 'tinymce' ) ).sendKeys( 'This is a test.' );

driver.sleep( 5000 );
driver.quit();
