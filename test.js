var webdriver = require( 'selenium-webdriver' ),
	By = webdriver.By,
	until = webdriver.until;

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
