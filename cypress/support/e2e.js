// Import commands.js using ES2015 syntax:
import './commands'

// Alternatively you can use CommonJS syntax:
// require('./commands')
import '@percy/cypress';

beforeEach( () => {
    cy.login( Cypress.env('wp_user'), Cypress.env('wp_pass') );
});