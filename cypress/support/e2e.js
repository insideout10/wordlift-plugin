// Import commands.js using ES2015 syntax:
import './commands'

// Alternatively you can use CommonJS syntax:
// require('./commands')
import '@percy/cypress';

beforeEach( () => {
    if (Cypress.mocha.getRunner().suite.ctx.currentTest._testConfig.unverifiedTestConfig.skipBeforeEach) {
        cy.log('skipping beforeEach hook')
        return
    }
    
    cy.login( Cypress.env('wp_user'), Cypress.env('wp_pass') );
});