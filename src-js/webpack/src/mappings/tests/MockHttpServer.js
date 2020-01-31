class MockHttpServer {
  constructor() {
    // Store a list of requests in the variable
    this.requests = [];
    // Store a list of responses to be dispatched at fetch request method.
    this.responses = [];
  }

  /**
   * Reset the server inside tests since it is on global scope, might need
   * to be reset
   */
  resetServer() {
    this.requests = [];
    this.responses = [];
  }
  /**
   * Insert the captured request from component, store it in the list
   * @param url The url which the component sent via fetch method
   * @param data The data sent in fetch method
   */
  insertCapturedRequestParams(url, data = null) {
    this.requests.push({
      url: url,
      data: data
    });
  }
  /**
   * Get last captured request, returns null if no request found.
   * @return {object} | null
   */
  getLastCapturedRequest() {
    if (this.requests.length > 0) {
      return this.requests[this.requests.length - 1];
    } else {
      return null;
    }
  }
  /**
   * Returns all the captured requests
   * @returns {Array} List of all captured requests
   */
  getCapturedRequests() {
    return this.requests;
  }

  /**
   * Get and remove the last captured request, returns null if no request found.
   * @return {object} | null
   */
  getAndRemoveLastCapturedRequest() {
    if (this.requests.length > 0) {
      return this.requests.shift();
    } else {
      return null;
    }
  }

  /**
   * Add a list of responses.
   * @param response
   */
  enqueueResponse(response) {
    this.responses.push(response);
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
