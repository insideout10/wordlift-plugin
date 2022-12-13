import './cy-wordpress';

// Cypress.Commands.add('login', (username, password) => {

//     cy.getCookies().then(cookies => {
//         console.log( 'cookie', cookies )

//         let hasMatch = false;
//         cookies.forEach((cookie) => {

//             if (cookie.name.substr(0, 20) === 'wordpress_logged_in_') {
//                 hasMatch = true;
//             }
//         });

//         if (!hasMatch) {
//             cy.visit('/wp-login.php').wait(1000);
//             cy.get('#user_login').type(username);
//             cy.get('#user_pass').type(`${password}{enter}`);

//             cy.url().should('contain', '/wp-admin');

//             cy.setCookie('wordpress_logged_in_login', 'WP+Cookie+check');
//         }
//     });
// });