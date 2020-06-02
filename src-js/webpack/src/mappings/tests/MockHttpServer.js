class MockHttpServer {
  constructor() {
    // Store a list of responses to be dispatched at fetch request method.
    this.responses = [];
  }

  /**
   * Add a list of responses.
   * @param responses
   */
  enqueueResponse(responses) {
    this.responses.push(responses);
  }

  /**
   * Dequeue the response from the mock server
   * @returns {null|Object}
   */
  dequeueResponse() {
    if (this.responses.length > 0) {
      return this.responses.shift();
    } else {
      return null;
    }
  }
}

export default MockHttpServer;
