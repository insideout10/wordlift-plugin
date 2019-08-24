!(function(t, e) {
  "object" == typeof exports && "object" == typeof module
    ? (module.exports = e())
    : "function" == typeof define && define.amd
    ? define([], e)
    : "object" == typeof exports
    ? (exports.wordliftCloud = e())
    : (t.wordliftCloud = e());
})(this, function() {
  return (function(t) {
    var e = {};
    function n(r) {
      if (e[r]) return e[r].exports;
      var o = (e[r] = { i: r, l: !1, exports: {} });
      return t[r].call(o.exports, o, o.exports, n), (o.l = !0), o.exports;
    }
    return (
      (n.m = t),
      (n.c = e),
      (n.d = function(t, e, r) {
        n.o(t, e) || Object.defineProperty(t, e, { enumerable: !0, get: r });
      }),
      (n.r = function(t) {
        "undefined" != typeof Symbol &&
          Symbol.toStringTag &&
          Object.defineProperty(t, Symbol.toStringTag, { value: "Module" }),
          Object.defineProperty(t, "__esModule", { value: !0 });
      }),
      (n.t = function(t, e) {
        if ((1 & e && (t = n(t)), 8 & e)) return t;
        if (4 & e && "object" == typeof t && t && t.__esModule) return t;
        var r = Object.create(null);
        if (
          (n.r(r),
          Object.defineProperty(r, "default", { enumerable: !0, value: t }),
          2 & e && "string" != typeof t)
        )
          for (var o in t)
            n.d(
              r,
              o,
              function(e) {
                return t[e];
              }.bind(null, o)
            );
        return r;
      }),
      (n.n = function(t) {
        var e =
          t && t.__esModule
            ? function() {
                return t.default;
              }
            : function() {
                return t;
              };
        return n.d(e, "a", e), e;
      }),
      (n.o = function(t, e) {
        return Object.prototype.hasOwnProperty.call(t, e);
      }),
      (n.p = ""),
      n((n.s = 29))
    );
  })([
    function(t, e, n) {
      "use strict";
      var r = n(1),
        o = n(13),
        i = Object.prototype.toString;
      function a(t) {
        return "[object Array]" === i.call(t);
      }
      function p(t) {
        return null !== t && "object" == typeof t;
      }
      function s(t) {
        return "[object Function]" === i.call(t);
      }
      function c(t, e) {
        if (null != t)
          if (("object" != typeof t && (t = [t]), a(t)))
            for (var n = 0, r = t.length; n < r; n++) e.call(null, t[n], n, t);
          else
            for (var o in t)
              Object.prototype.hasOwnProperty.call(t, o) &&
                e.call(null, t[o], o, t);
      }
      t.exports = {
        isArray: a,
        isArrayBuffer: function(t) {
          return "[object ArrayBuffer]" === i.call(t);
        },
        isBuffer: o,
        isFormData: function(t) {
          return "undefined" != typeof FormData && t instanceof FormData;
        },
        isArrayBufferView: function(t) {
          return "undefined" != typeof ArrayBuffer && ArrayBuffer.isView
            ? ArrayBuffer.isView(t)
            : t && t.buffer && t.buffer instanceof ArrayBuffer;
        },
        isString: function(t) {
          return "string" == typeof t;
        },
        isNumber: function(t) {
          return "number" == typeof t;
        },
        isObject: p,
        isUndefined: function(t) {
          return void 0 === t;
        },
        isDate: function(t) {
          return "[object Date]" === i.call(t);
        },
        isFile: function(t) {
          return "[object File]" === i.call(t);
        },
        isBlob: function(t) {
          return "[object Blob]" === i.call(t);
        },
        isFunction: s,
        isStream: function(t) {
          return p(t) && s(t.pipe);
        },
        isURLSearchParams: function(t) {
          return (
            "undefined" != typeof URLSearchParams &&
            t instanceof URLSearchParams
          );
        },
        isStandardBrowserEnv: function() {
          return (
            ("undefined" == typeof navigator ||
              ("ReactNative" !== navigator.product &&
                "NativeScript" !== navigator.product &&
                "NS" !== navigator.product)) &&
            ("undefined" != typeof window && "undefined" != typeof document)
          );
        },
        forEach: c,
        merge: function t() {
          var e = {};
          function n(n, r) {
            "object" == typeof e[r] && "object" == typeof n
              ? (e[r] = t(e[r], n))
              : (e[r] = n);
          }
          for (var r = 0, o = arguments.length; r < o; r++) c(arguments[r], n);
          return e;
        },
        deepMerge: function t() {
          var e = {};
          function n(n, r) {
            "object" == typeof e[r] && "object" == typeof n
              ? (e[r] = t(e[r], n))
              : (e[r] = "object" == typeof n ? t({}, n) : n);
          }
          for (var r = 0, o = arguments.length; r < o; r++) c(arguments[r], n);
          return e;
        },
        extend: function(t, e, n) {
          return (
            c(e, function(e, o) {
              t[o] = n && "function" == typeof e ? r(e, n) : e;
            }),
            t
          );
        },
        trim: function(t) {
          return t.replace(/^\s*/, "").replace(/\s*$/, "");
        }
      };
    },
    function(t, e, n) {
      "use strict";
      t.exports = function(t, e) {
        return function() {
          for (var n = new Array(arguments.length), r = 0; r < n.length; r++)
            n[r] = arguments[r];
          return t.apply(e, n);
        };
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      function o(t) {
        return encodeURIComponent(t)
          .replace(/%40/gi, "@")
          .replace(/%3A/gi, ":")
          .replace(/%24/g, "$")
          .replace(/%2C/gi, ",")
          .replace(/%20/g, "+")
          .replace(/%5B/gi, "[")
          .replace(/%5D/gi, "]");
      }
      t.exports = function(t, e, n) {
        if (!e) return t;
        var i;
        if (n) i = n(e);
        else if (r.isURLSearchParams(e)) i = e.toString();
        else {
          var a = [];
          r.forEach(e, function(t, e) {
            null != t &&
              (r.isArray(t) ? (e += "[]") : (t = [t]),
              r.forEach(t, function(t) {
                r.isDate(t)
                  ? (t = t.toISOString())
                  : r.isObject(t) && (t = JSON.stringify(t)),
                  a.push(o(e) + "=" + o(t));
              }));
          }),
            (i = a.join("&"));
        }
        if (i) {
          var p = t.indexOf("#");
          -1 !== p && (t = t.slice(0, p)),
            (t += (-1 === t.indexOf("?") ? "?" : "&") + i);
        }
        return t;
      };
    },
    function(t, e, n) {
      "use strict";
      t.exports = function(t) {
        return !(!t || !t.__CANCEL__);
      };
    },
    function(t, e, n) {
      "use strict";
      (function(e) {
        var r = n(0),
          o = n(19),
          i = { "Content-Type": "application/x-www-form-urlencoded" };
        function a(t, e) {
          !r.isUndefined(t) &&
            r.isUndefined(t["Content-Type"]) &&
            (t["Content-Type"] = e);
        }
        var p,
          s = {
            adapter: (void 0 !== e &&
            "[object process]" === Object.prototype.toString.call(e)
              ? (p = n(5))
              : "undefined" != typeof XMLHttpRequest && (p = n(5)),
            p),
            transformRequest: [
              function(t, e) {
                return (
                  o(e, "Accept"),
                  o(e, "Content-Type"),
                  r.isFormData(t) ||
                  r.isArrayBuffer(t) ||
                  r.isBuffer(t) ||
                  r.isStream(t) ||
                  r.isFile(t) ||
                  r.isBlob(t)
                    ? t
                    : r.isArrayBufferView(t)
                    ? t.buffer
                    : r.isURLSearchParams(t)
                    ? (a(e, "application/x-www-form-urlencoded;charset=utf-8"),
                      t.toString())
                    : r.isObject(t)
                    ? (a(e, "application/json;charset=utf-8"),
                      JSON.stringify(t))
                    : t
                );
              }
            ],
            transformResponse: [
              function(t) {
                if ("string" == typeof t)
                  try {
                    t = JSON.parse(t);
                  } catch (t) {}
                return t;
              }
            ],
            timeout: 0,
            xsrfCookieName: "XSRF-TOKEN",
            xsrfHeaderName: "X-XSRF-TOKEN",
            maxContentLength: -1,
            validateStatus: function(t) {
              return t >= 200 && t < 300;
            }
          };
        (s.headers = {
          common: { Accept: "application/json, text/plain, */*" }
        }),
          r.forEach(["delete", "get", "head"], function(t) {
            s.headers[t] = {};
          }),
          r.forEach(["post", "put", "patch"], function(t) {
            s.headers[t] = r.merge(i);
          }),
          (t.exports = s);
      }.call(this, n(18)));
    },
    function(t, e, n) {
      "use strict";
      var r = n(0),
        o = n(20),
        i = n(2),
        a = n(22),
        p = n(23),
        s = n(6);
      t.exports = function(t) {
        return new Promise(function(e, c) {
          var f = t.data,
            l = t.headers;
          r.isFormData(f) && delete l["Content-Type"];
          var u = new XMLHttpRequest();
          if (t.auth) {
            var d = t.auth.username || "",
              m = t.auth.password || "";
            l.Authorization = "Basic " + btoa(d + ":" + m);
          }
          if (
            (u.open(
              t.method.toUpperCase(),
              i(t.url, t.params, t.paramsSerializer),
              !0
            ),
            (u.timeout = t.timeout),
            (u.onreadystatechange = function() {
              if (
                u &&
                4 === u.readyState &&
                (0 !== u.status ||
                  (u.responseURL && 0 === u.responseURL.indexOf("file:")))
              ) {
                var n =
                    "getAllResponseHeaders" in u
                      ? a(u.getAllResponseHeaders())
                      : null,
                  r = {
                    data:
                      t.responseType && "text" !== t.responseType
                        ? u.response
                        : u.responseText,
                    status: u.status,
                    statusText: u.statusText,
                    headers: n,
                    config: t,
                    request: u
                  };
                o(e, c, r), (u = null);
              }
            }),
            (u.onabort = function() {
              u && (c(s("Request aborted", t, "ECONNABORTED", u)), (u = null));
            }),
            (u.onerror = function() {
              c(s("Network Error", t, null, u)), (u = null);
            }),
            (u.ontimeout = function() {
              c(
                s(
                  "timeout of " + t.timeout + "ms exceeded",
                  t,
                  "ECONNABORTED",
                  u
                )
              ),
                (u = null);
            }),
            r.isStandardBrowserEnv())
          ) {
            var h = n(24),
              b =
                (t.withCredentials || p(t.url)) && t.xsrfCookieName
                  ? h.read(t.xsrfCookieName)
                  : void 0;
            b && (l[t.xsrfHeaderName] = b);
          }
          if (
            ("setRequestHeader" in u &&
              r.forEach(l, function(t, e) {
                void 0 === f && "content-type" === e.toLowerCase()
                  ? delete l[e]
                  : u.setRequestHeader(e, t);
              }),
            t.withCredentials && (u.withCredentials = !0),
            t.responseType)
          )
            try {
              u.responseType = t.responseType;
            } catch (e) {
              if ("json" !== t.responseType) throw e;
            }
          "function" == typeof t.onDownloadProgress &&
            u.addEventListener("progress", t.onDownloadProgress),
            "function" == typeof t.onUploadProgress &&
              u.upload &&
              u.upload.addEventListener("progress", t.onUploadProgress),
            t.cancelToken &&
              t.cancelToken.promise.then(function(t) {
                u && (u.abort(), c(t), (u = null));
              }),
            void 0 === f && (f = null),
            u.send(f);
        });
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(21);
      t.exports = function(t, e, n, o, i) {
        var a = new Error(t);
        return r(a, e, n, o, i);
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      t.exports = function(t, e) {
        e = e || {};
        var n = {};
        return (
          r.forEach(["url", "method", "params", "data"], function(t) {
            void 0 !== e[t] && (n[t] = e[t]);
          }),
          r.forEach(["headers", "auth", "proxy"], function(o) {
            r.isObject(e[o])
              ? (n[o] = r.deepMerge(t[o], e[o]))
              : void 0 !== e[o]
              ? (n[o] = e[o])
              : r.isObject(t[o])
              ? (n[o] = r.deepMerge(t[o]))
              : void 0 !== t[o] && (n[o] = t[o]);
          }),
          r.forEach(
            [
              "baseURL",
              "transformRequest",
              "transformResponse",
              "paramsSerializer",
              "timeout",
              "withCredentials",
              "adapter",
              "responseType",
              "xsrfCookieName",
              "xsrfHeaderName",
              "onUploadProgress",
              "onDownloadProgress",
              "maxContentLength",
              "validateStatus",
              "maxRedirects",
              "httpAgent",
              "httpsAgent",
              "cancelToken",
              "socketPath"
            ],
            function(r) {
              void 0 !== e[r]
                ? (n[r] = e[r])
                : void 0 !== t[r] && (n[r] = t[r]);
            }
          ),
          n
        );
      };
    },
    function(t, e, n) {
      "use strict";
      function r(t) {
        this.message = t;
      }
      (r.prototype.toString = function() {
        return "Cancel" + (this.message ? ": " + this.message : "");
      }),
        (r.prototype.__CANCEL__ = !0),
        (t.exports = r);
    },
    function(t, e, n) {
      "use strict";
      (function(t) {
        for (
          /**!
           * @fileOverview Kickass library to create and place poppers near their reference elements.
           * @version 1.15.0
           * @license
           * Copyright (c) 2016 Federico Zivolo and contributors
           *
           * Permission is hereby granted, free of charge, to any person obtaining a copy
           * of this software and associated documentation files (the "Software"), to deal
           * in the Software without restriction, including without limitation the rights
           * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
           * copies of the Software, and to permit persons to whom the Software is
           * furnished to do so, subject to the following conditions:
           *
           * The above copyright notice and this permission notice shall be included in all
           * copies or substantial portions of the Software.
           *
           * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
           * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
           * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
           * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
           * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
           * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
           * SOFTWARE.
           */
          var n =
              "undefined" != typeof window && "undefined" != typeof document,
            r = ["Edge", "Trident", "Firefox"],
            o = 0,
            i = 0;
          i < r.length;
          i += 1
        )
          if (n && navigator.userAgent.indexOf(r[i]) >= 0) {
            o = 1;
            break;
          }
        var a =
          n && window.Promise
            ? function(t) {
                var e = !1;
                return function() {
                  e ||
                    ((e = !0),
                    window.Promise.resolve().then(function() {
                      (e = !1), t();
                    }));
                };
              }
            : function(t) {
                var e = !1;
                return function() {
                  e ||
                    ((e = !0),
                    setTimeout(function() {
                      (e = !1), t();
                    }, o));
                };
              };
        function p(t) {
          return t && "[object Function]" === {}.toString.call(t);
        }
        function s(t, e) {
          if (1 !== t.nodeType) return [];
          var n = t.ownerDocument.defaultView.getComputedStyle(t, null);
          return e ? n[e] : n;
        }
        function c(t) {
          return "HTML" === t.nodeName ? t : t.parentNode || t.host;
        }
        function f(t) {
          if (!t) return document.body;
          switch (t.nodeName) {
            case "HTML":
            case "BODY":
              return t.ownerDocument.body;
            case "#document":
              return t.body;
          }
          var e = s(t),
            n = e.overflow,
            r = e.overflowX,
            o = e.overflowY;
          return /(auto|scroll|overlay)/.test(n + o + r) ? t : f(c(t));
        }
        var l = n && !(!window.MSInputMethodContext || !document.documentMode),
          u = n && /MSIE 10/.test(navigator.userAgent);
        function d(t) {
          return 11 === t ? l : 10 === t ? u : l || u;
        }
        function m(t) {
          if (!t) return document.documentElement;
          for (
            var e = d(10) ? document.body : null, n = t.offsetParent || null;
            n === e && t.nextElementSibling;

          )
            n = (t = t.nextElementSibling).offsetParent;
          var r = n && n.nodeName;
          return r && "BODY" !== r && "HTML" !== r
            ? -1 !== ["TH", "TD", "TABLE"].indexOf(n.nodeName) &&
              "static" === s(n, "position")
              ? m(n)
              : n
            : t
            ? t.ownerDocument.documentElement
            : document.documentElement;
        }
        function h(t) {
          return null !== t.parentNode ? h(t.parentNode) : t;
        }
        function b(t, e) {
          if (!(t && t.nodeType && e && e.nodeType))
            return document.documentElement;
          var n =
              t.compareDocumentPosition(e) & Node.DOCUMENT_POSITION_FOLLOWING,
            r = n ? t : e,
            o = n ? e : t,
            i = document.createRange();
          i.setStart(r, 0), i.setEnd(o, 0);
          var a,
            p,
            s = i.commonAncestorContainer;
          if ((t !== s && e !== s) || r.contains(o))
            return "BODY" === (p = (a = s).nodeName) ||
              ("HTML" !== p && m(a.firstElementChild) !== a)
              ? m(s)
              : s;
          var c = h(t);
          return c.host ? b(c.host, e) : b(t, h(e).host);
        }
        function v(t) {
          var e =
              "top" ===
              (arguments.length > 1 && void 0 !== arguments[1]
                ? arguments[1]
                : "top")
                ? "scrollTop"
                : "scrollLeft",
            n = t.nodeName;
          if ("BODY" === n || "HTML" === n) {
            var r = t.ownerDocument.documentElement;
            return (t.ownerDocument.scrollingElement || r)[e];
          }
          return t[e];
        }
        function g(t, e) {
          var n = "x" === e ? "Left" : "Top",
            r = "Left" === n ? "Right" : "Bottom";
          return (
            parseFloat(t["border" + n + "Width"], 10) +
            parseFloat(t["border" + r + "Width"], 10)
          );
        }
        function y(t, e, n, r) {
          return Math.max(
            e["offset" + t],
            e["scroll" + t],
            n["client" + t],
            n["offset" + t],
            n["scroll" + t],
            d(10)
              ? parseInt(n["offset" + t]) +
                  parseInt(r["margin" + ("Height" === t ? "Top" : "Left")]) +
                  parseInt(r["margin" + ("Height" === t ? "Bottom" : "Right")])
              : 0
          );
        }
        function w(t) {
          var e = t.body,
            n = t.documentElement,
            r = d(10) && getComputedStyle(n);
          return { height: y("Height", e, n, r), width: y("Width", e, n, r) };
        }
        var x = function(t, e) {
            if (!(t instanceof e))
              throw new TypeError("Cannot call a class as a function");
          },
          E = (function() {
            function t(t, e) {
              for (var n = 0; n < e.length; n++) {
                var r = e[n];
                (r.enumerable = r.enumerable || !1),
                  (r.configurable = !0),
                  "value" in r && (r.writable = !0),
                  Object.defineProperty(t, r.key, r);
              }
            }
            return function(e, n, r) {
              return n && t(e.prototype, n), r && t(e, r), e;
            };
          })(),
          k = function(t, e, n) {
            return (
              e in t
                ? Object.defineProperty(t, e, {
                    value: n,
                    enumerable: !0,
                    configurable: !0,
                    writable: !0
                  })
                : (t[e] = n),
              t
            );
          },
          O =
            Object.assign ||
            function(t) {
              for (var e = 1; e < arguments.length; e++) {
                var n = arguments[e];
                for (var r in n)
                  Object.prototype.hasOwnProperty.call(n, r) && (t[r] = n[r]);
              }
              return t;
            };
        function T(t) {
          return O({}, t, {
            right: t.left + t.width,
            bottom: t.top + t.height
          });
        }
        function C(t) {
          var e = {};
          try {
            if (d(10)) {
              e = t.getBoundingClientRect();
              var n = v(t, "top"),
                r = v(t, "left");
              (e.top += n), (e.left += r), (e.bottom += n), (e.right += r);
            } else e = t.getBoundingClientRect();
          } catch (t) {}
          var o = {
              left: e.left,
              top: e.top,
              width: e.right - e.left,
              height: e.bottom - e.top
            },
            i = "HTML" === t.nodeName ? w(t.ownerDocument) : {},
            a = i.width || t.clientWidth || o.right - o.left,
            p = i.height || t.clientHeight || o.bottom - o.top,
            c = t.offsetWidth - a,
            f = t.offsetHeight - p;
          if (c || f) {
            var l = s(t);
            (c -= g(l, "x")), (f -= g(l, "y")), (o.width -= c), (o.height -= f);
          }
          return T(o);
        }
        function A(t, e) {
          var n =
              arguments.length > 2 && void 0 !== arguments[2] && arguments[2],
            r = d(10),
            o = "HTML" === e.nodeName,
            i = C(t),
            a = C(e),
            p = f(t),
            c = s(e),
            l = parseFloat(c.borderTopWidth, 10),
            u = parseFloat(c.borderLeftWidth, 10);
          n &&
            o &&
            ((a.top = Math.max(a.top, 0)), (a.left = Math.max(a.left, 0)));
          var m = T({
            top: i.top - a.top - l,
            left: i.left - a.left - u,
            width: i.width,
            height: i.height
          });
          if (((m.marginTop = 0), (m.marginLeft = 0), !r && o)) {
            var h = parseFloat(c.marginTop, 10),
              b = parseFloat(c.marginLeft, 10);
            (m.top -= l - h),
              (m.bottom -= l - h),
              (m.left -= u - b),
              (m.right -= u - b),
              (m.marginTop = h),
              (m.marginLeft = b);
          }
          return (
            (r && !n ? e.contains(p) : e === p && "BODY" !== p.nodeName) &&
              (m = (function(t, e) {
                var n =
                    arguments.length > 2 &&
                    void 0 !== arguments[2] &&
                    arguments[2],
                  r = v(e, "top"),
                  o = v(e, "left"),
                  i = n ? -1 : 1;
                return (
                  (t.top += r * i),
                  (t.bottom += r * i),
                  (t.left += o * i),
                  (t.right += o * i),
                  t
                );
              })(m, e)),
            m
          );
        }
        function L(t) {
          if (!t || !t.parentElement || d()) return document.documentElement;
          for (var e = t.parentElement; e && "none" === s(e, "transform"); )
            e = e.parentElement;
          return e || document.documentElement;
        }
        function S(t, e, n, r) {
          var o =
              arguments.length > 4 && void 0 !== arguments[4] && arguments[4],
            i = { top: 0, left: 0 },
            a = o ? L(t) : b(t, e);
          if ("viewport" === r)
            i = (function(t) {
              var e =
                  arguments.length > 1 &&
                  void 0 !== arguments[1] &&
                  arguments[1],
                n = t.ownerDocument.documentElement,
                r = A(t, n),
                o = Math.max(n.clientWidth, window.innerWidth || 0),
                i = Math.max(n.clientHeight, window.innerHeight || 0),
                a = e ? 0 : v(n),
                p = e ? 0 : v(n, "left");
              return T({
                top: a - r.top + r.marginTop,
                left: p - r.left + r.marginLeft,
                width: o,
                height: i
              });
            })(a, o);
          else {
            var p = void 0;
            "scrollParent" === r
              ? "BODY" === (p = f(c(e))).nodeName &&
                (p = t.ownerDocument.documentElement)
              : (p = "window" === r ? t.ownerDocument.documentElement : r);
            var l = A(p, a, o);
            if (
              "HTML" !== p.nodeName ||
              (function t(e) {
                var n = e.nodeName;
                if ("BODY" === n || "HTML" === n) return !1;
                if ("fixed" === s(e, "position")) return !0;
                var r = c(e);
                return !!r && t(r);
              })(a)
            )
              i = l;
            else {
              var u = w(t.ownerDocument),
                d = u.height,
                m = u.width;
              (i.top += l.top - l.marginTop),
                (i.bottom = d + l.top),
                (i.left += l.left - l.marginLeft),
                (i.right = m + l.left);
            }
          }
          var h = "number" == typeof (n = n || 0);
          return (
            (i.left += h ? n : n.left || 0),
            (i.top += h ? n : n.top || 0),
            (i.right -= h ? n : n.right || 0),
            (i.bottom -= h ? n : n.bottom || 0),
            i
          );
        }
        function N(t, e, n, r, o) {
          var i =
            arguments.length > 5 && void 0 !== arguments[5] ? arguments[5] : 0;
          if (-1 === t.indexOf("auto")) return t;
          var a = S(n, r, i, o),
            p = {
              top: { width: a.width, height: e.top - a.top },
              right: { width: a.right - e.right, height: a.height },
              bottom: { width: a.width, height: a.bottom - e.bottom },
              left: { width: e.left - a.left, height: a.height }
            },
            s = Object.keys(p)
              .map(function(t) {
                return O({ key: t }, p[t], {
                  area: ((e = p[t]), e.width * e.height)
                });
                var e;
              })
              .sort(function(t, e) {
                return e.area - t.area;
              }),
            c = s.filter(function(t) {
              var e = t.width,
                r = t.height;
              return e >= n.clientWidth && r >= n.clientHeight;
            }),
            f = c.length > 0 ? c[0].key : s[0].key,
            l = t.split("-")[1];
          return f + (l ? "-" + l : "");
        }
        function j(t, e, n) {
          var r =
            arguments.length > 3 && void 0 !== arguments[3]
              ? arguments[3]
              : null;
          return A(n, r ? L(e) : b(e, n), r);
        }
        function D(t) {
          var e = t.ownerDocument.defaultView.getComputedStyle(t),
            n = parseFloat(e.marginTop || 0) + parseFloat(e.marginBottom || 0),
            r = parseFloat(e.marginLeft || 0) + parseFloat(e.marginRight || 0);
          return { width: t.offsetWidth + r, height: t.offsetHeight + n };
        }
        function M(t) {
          var e = {
            left: "right",
            right: "left",
            bottom: "top",
            top: "bottom"
          };
          return t.replace(/left|right|bottom|top/g, function(t) {
            return e[t];
          });
        }
        function F(t, e, n) {
          n = n.split("-")[0];
          var r = D(t),
            o = { width: r.width, height: r.height },
            i = -1 !== ["right", "left"].indexOf(n),
            a = i ? "top" : "left",
            p = i ? "left" : "top",
            s = i ? "height" : "width",
            c = i ? "width" : "height";
          return (
            (o[a] = e[a] + e[s] / 2 - r[s] / 2),
            (o[p] = n === p ? e[p] - r[c] : e[M(p)]),
            o
          );
        }
        function B(t, e) {
          return Array.prototype.find ? t.find(e) : t.filter(e)[0];
        }
        function P(t, e, n) {
          return (
            (void 0 === n
              ? t
              : t.slice(
                  0,
                  (function(t, e, n) {
                    if (Array.prototype.findIndex)
                      return t.findIndex(function(t) {
                        return t[e] === n;
                      });
                    var r = B(t, function(t) {
                      return t[e] === n;
                    });
                    return t.indexOf(r);
                  })(t, "name", n)
                )
            ).forEach(function(t) {
              t.function &&
                console.warn(
                  "`modifier.function` is deprecated, use `modifier.fn`!"
                );
              var n = t.function || t.fn;
              t.enabled &&
                p(n) &&
                ((e.offsets.popper = T(e.offsets.popper)),
                (e.offsets.reference = T(e.offsets.reference)),
                (e = n(e, t)));
            }),
            e
          );
        }
        function I() {
          if (!this.state.isDestroyed) {
            var t = {
              instance: this,
              styles: {},
              arrowStyles: {},
              attributes: {},
              flipped: !1,
              offsets: {}
            };
            (t.offsets.reference = j(
              this.state,
              this.popper,
              this.reference,
              this.options.positionFixed
            )),
              (t.placement = N(
                this.options.placement,
                t.offsets.reference,
                this.popper,
                this.reference,
                this.options.modifiers.flip.boundariesElement,
                this.options.modifiers.flip.padding
              )),
              (t.originalPlacement = t.placement),
              (t.positionFixed = this.options.positionFixed),
              (t.offsets.popper = F(
                this.popper,
                t.offsets.reference,
                t.placement
              )),
              (t.offsets.popper.position = this.options.positionFixed
                ? "fixed"
                : "absolute"),
              (t = P(this.modifiers, t)),
              this.state.isCreated
                ? this.options.onUpdate(t)
                : ((this.state.isCreated = !0), this.options.onCreate(t));
          }
        }
        function R(t, e) {
          return t.some(function(t) {
            var n = t.name;
            return t.enabled && n === e;
          });
        }
        function H(t) {
          for (
            var e = [!1, "ms", "Webkit", "Moz", "O"],
              n = t.charAt(0).toUpperCase() + t.slice(1),
              r = 0;
            r < e.length;
            r++
          ) {
            var o = e[r],
              i = o ? "" + o + n : t;
            if (void 0 !== document.body.style[i]) return i;
          }
          return null;
        }
        function Y() {
          return (
            (this.state.isDestroyed = !0),
            R(this.modifiers, "applyStyle") &&
              (this.popper.removeAttribute("x-placement"),
              (this.popper.style.position = ""),
              (this.popper.style.top = ""),
              (this.popper.style.left = ""),
              (this.popper.style.right = ""),
              (this.popper.style.bottom = ""),
              (this.popper.style.willChange = ""),
              (this.popper.style[H("transform")] = "")),
            this.disableEventListeners(),
            this.options.removeOnDestroy &&
              this.popper.parentNode.removeChild(this.popper),
            this
          );
        }
        function U(t) {
          var e = t.ownerDocument;
          return e ? e.defaultView : window;
        }
        function X(t, e, n, r) {
          (n.updateBound = r),
            U(t).addEventListener("resize", n.updateBound, { passive: !0 });
          var o = f(t);
          return (
            (function t(e, n, r, o) {
              var i = "BODY" === e.nodeName,
                a = i ? e.ownerDocument.defaultView : e;
              a.addEventListener(n, r, { passive: !0 }),
                i || t(f(a.parentNode), n, r, o),
                o.push(a);
            })(o, "scroll", n.updateBound, n.scrollParents),
            (n.scrollElement = o),
            (n.eventsEnabled = !0),
            n
          );
        }
        function q() {
          this.state.eventsEnabled ||
            (this.state = X(
              this.reference,
              this.options,
              this.state,
              this.scheduleUpdate
            ));
        }
        function z() {
          var t, e;
          this.state.eventsEnabled &&
            (cancelAnimationFrame(this.scheduleUpdate),
            (this.state = ((t = this.reference),
            (e = this.state),
            U(t).removeEventListener("resize", e.updateBound),
            e.scrollParents.forEach(function(t) {
              t.removeEventListener("scroll", e.updateBound);
            }),
            (e.updateBound = null),
            (e.scrollParents = []),
            (e.scrollElement = null),
            (e.eventsEnabled = !1),
            e)));
        }
        function _(t) {
          return "" !== t && !isNaN(parseFloat(t)) && isFinite(t);
        }
        function W(t, e) {
          Object.keys(e).forEach(function(n) {
            var r = "";
            -1 !==
              ["width", "height", "top", "right", "bottom", "left"].indexOf(
                n
              ) &&
              _(e[n]) &&
              (r = "px"),
              (t.style[n] = e[n] + r);
          });
        }
        var V = n && /Firefox/i.test(navigator.userAgent);
        function $(t, e, n) {
          var r = B(t, function(t) {
              return t.name === e;
            }),
            o =
              !!r &&
              t.some(function(t) {
                return t.name === n && t.enabled && t.order < r.order;
              });
          if (!o) {
            var i = "`" + e + "`",
              a = "`" + n + "`";
            console.warn(
              a +
                " modifier is required by " +
                i +
                " modifier in order to work, be sure to include it before " +
                i +
                "!"
            );
          }
          return o;
        }
        var K = [
            "auto-start",
            "auto",
            "auto-end",
            "top-start",
            "top",
            "top-end",
            "right-start",
            "right",
            "right-end",
            "bottom-end",
            "bottom",
            "bottom-start",
            "left-end",
            "left",
            "left-start"
          ],
          J = K.slice(3);
        function G(t) {
          var e =
              arguments.length > 1 && void 0 !== arguments[1] && arguments[1],
            n = J.indexOf(t),
            r = J.slice(n + 1).concat(J.slice(0, n));
          return e ? r.reverse() : r;
        }
        var Q = {
          FLIP: "flip",
          CLOCKWISE: "clockwise",
          COUNTERCLOCKWISE: "counterclockwise"
        };
        function Z(t, e, n, r) {
          var o = [0, 0],
            i = -1 !== ["right", "left"].indexOf(r),
            a = t.split(/(\+|\-)/).map(function(t) {
              return t.trim();
            }),
            p = a.indexOf(
              B(a, function(t) {
                return -1 !== t.search(/,|\s/);
              })
            );
          a[p] &&
            -1 === a[p].indexOf(",") &&
            console.warn(
              "Offsets separated by white space(s) are deprecated, use a comma (,) instead."
            );
          var s = /\s*,\s*|\s+/,
            c =
              -1 !== p
                ? [
                    a.slice(0, p).concat([a[p].split(s)[0]]),
                    [a[p].split(s)[1]].concat(a.slice(p + 1))
                  ]
                : [a];
          return (
            (c = c.map(function(t, r) {
              var o = (1 === r ? !i : i) ? "height" : "width",
                a = !1;
              return t
                .reduce(function(t, e) {
                  return "" === t[t.length - 1] && -1 !== ["+", "-"].indexOf(e)
                    ? ((t[t.length - 1] = e), (a = !0), t)
                    : a
                    ? ((t[t.length - 1] += e), (a = !1), t)
                    : t.concat(e);
                }, [])
                .map(function(t) {
                  return (function(t, e, n, r) {
                    var o = t.match(/((?:\-|\+)?\d*\.?\d*)(.*)/),
                      i = +o[1],
                      a = o[2];
                    if (!i) return t;
                    if (0 === a.indexOf("%")) {
                      var p = void 0;
                      switch (a) {
                        case "%p":
                          p = n;
                          break;
                        case "%":
                        case "%r":
                        default:
                          p = r;
                      }
                      return (T(p)[e] / 100) * i;
                    }
                    if ("vh" === a || "vw" === a) {
                      return (
                        (("vh" === a
                          ? Math.max(
                              document.documentElement.clientHeight,
                              window.innerHeight || 0
                            )
                          : Math.max(
                              document.documentElement.clientWidth,
                              window.innerWidth || 0
                            )) /
                          100) *
                        i
                      );
                    }
                    return i;
                  })(t, o, e, n);
                });
            })).forEach(function(t, e) {
              t.forEach(function(n, r) {
                _(n) && (o[e] += n * ("-" === t[r - 1] ? -1 : 1));
              });
            }),
            o
          );
        }
        var tt = {
            placement: "bottom",
            positionFixed: !1,
            eventsEnabled: !0,
            removeOnDestroy: !1,
            onCreate: function() {},
            onUpdate: function() {},
            modifiers: {
              shift: {
                order: 100,
                enabled: !0,
                fn: function(t) {
                  var e = t.placement,
                    n = e.split("-")[0],
                    r = e.split("-")[1];
                  if (r) {
                    var o = t.offsets,
                      i = o.reference,
                      a = o.popper,
                      p = -1 !== ["bottom", "top"].indexOf(n),
                      s = p ? "left" : "top",
                      c = p ? "width" : "height",
                      f = {
                        start: k({}, s, i[s]),
                        end: k({}, s, i[s] + i[c] - a[c])
                      };
                    t.offsets.popper = O({}, a, f[r]);
                  }
                  return t;
                }
              },
              offset: {
                order: 200,
                enabled: !0,
                fn: function(t, e) {
                  var n = e.offset,
                    r = t.placement,
                    o = t.offsets,
                    i = o.popper,
                    a = o.reference,
                    p = r.split("-")[0],
                    s = void 0;
                  return (
                    (s = _(+n) ? [+n, 0] : Z(n, i, a, p)),
                    "left" === p
                      ? ((i.top += s[0]), (i.left -= s[1]))
                      : "right" === p
                      ? ((i.top += s[0]), (i.left += s[1]))
                      : "top" === p
                      ? ((i.left += s[0]), (i.top -= s[1]))
                      : "bottom" === p && ((i.left += s[0]), (i.top += s[1])),
                    (t.popper = i),
                    t
                  );
                },
                offset: 0
              },
              preventOverflow: {
                order: 300,
                enabled: !0,
                fn: function(t, e) {
                  var n = e.boundariesElement || m(t.instance.popper);
                  t.instance.reference === n && (n = m(n));
                  var r = H("transform"),
                    o = t.instance.popper.style,
                    i = o.top,
                    a = o.left,
                    p = o[r];
                  (o.top = ""), (o.left = ""), (o[r] = "");
                  var s = S(
                    t.instance.popper,
                    t.instance.reference,
                    e.padding,
                    n,
                    t.positionFixed
                  );
                  (o.top = i), (o.left = a), (o[r] = p), (e.boundaries = s);
                  var c = e.priority,
                    f = t.offsets.popper,
                    l = {
                      primary: function(t) {
                        var n = f[t];
                        return (
                          f[t] < s[t] &&
                            !e.escapeWithReference &&
                            (n = Math.max(f[t], s[t])),
                          k({}, t, n)
                        );
                      },
                      secondary: function(t) {
                        var n = "right" === t ? "left" : "top",
                          r = f[n];
                        return (
                          f[t] > s[t] &&
                            !e.escapeWithReference &&
                            (r = Math.min(
                              f[n],
                              s[t] - ("right" === t ? f.width : f.height)
                            )),
                          k({}, n, r)
                        );
                      }
                    };
                  return (
                    c.forEach(function(t) {
                      var e =
                        -1 !== ["left", "top"].indexOf(t)
                          ? "primary"
                          : "secondary";
                      f = O({}, f, l[e](t));
                    }),
                    (t.offsets.popper = f),
                    t
                  );
                },
                priority: ["left", "right", "top", "bottom"],
                padding: 5,
                boundariesElement: "scrollParent"
              },
              keepTogether: {
                order: 400,
                enabled: !0,
                fn: function(t) {
                  var e = t.offsets,
                    n = e.popper,
                    r = e.reference,
                    o = t.placement.split("-")[0],
                    i = Math.floor,
                    a = -1 !== ["top", "bottom"].indexOf(o),
                    p = a ? "right" : "bottom",
                    s = a ? "left" : "top",
                    c = a ? "width" : "height";
                  return (
                    n[p] < i(r[s]) && (t.offsets.popper[s] = i(r[s]) - n[c]),
                    n[s] > i(r[p]) && (t.offsets.popper[s] = i(r[p])),
                    t
                  );
                }
              },
              arrow: {
                order: 500,
                enabled: !0,
                fn: function(t, e) {
                  var n;
                  if (!$(t.instance.modifiers, "arrow", "keepTogether"))
                    return t;
                  var r = e.element;
                  if ("string" == typeof r) {
                    if (!(r = t.instance.popper.querySelector(r))) return t;
                  } else if (!t.instance.popper.contains(r))
                    return (
                      console.warn(
                        "WARNING: `arrow.element` must be child of its popper element!"
                      ),
                      t
                    );
                  var o = t.placement.split("-")[0],
                    i = t.offsets,
                    a = i.popper,
                    p = i.reference,
                    c = -1 !== ["left", "right"].indexOf(o),
                    f = c ? "height" : "width",
                    l = c ? "Top" : "Left",
                    u = l.toLowerCase(),
                    d = c ? "left" : "top",
                    m = c ? "bottom" : "right",
                    h = D(r)[f];
                  p[m] - h < a[u] && (t.offsets.popper[u] -= a[u] - (p[m] - h)),
                    p[u] + h > a[m] && (t.offsets.popper[u] += p[u] + h - a[m]),
                    (t.offsets.popper = T(t.offsets.popper));
                  var b = p[u] + p[f] / 2 - h / 2,
                    v = s(t.instance.popper),
                    g = parseFloat(v["margin" + l], 10),
                    y = parseFloat(v["border" + l + "Width"], 10),
                    w = b - t.offsets.popper[u] - g - y;
                  return (
                    (w = Math.max(Math.min(a[f] - h, w), 0)),
                    (t.arrowElement = r),
                    (t.offsets.arrow = (k((n = {}), u, Math.round(w)),
                    k(n, d, ""),
                    n)),
                    t
                  );
                },
                element: "[x-arrow]"
              },
              flip: {
                order: 600,
                enabled: !0,
                fn: function(t, e) {
                  if (R(t.instance.modifiers, "inner")) return t;
                  if (t.flipped && t.placement === t.originalPlacement)
                    return t;
                  var n = S(
                      t.instance.popper,
                      t.instance.reference,
                      e.padding,
                      e.boundariesElement,
                      t.positionFixed
                    ),
                    r = t.placement.split("-")[0],
                    o = M(r),
                    i = t.placement.split("-")[1] || "",
                    a = [];
                  switch (e.behavior) {
                    case Q.FLIP:
                      a = [r, o];
                      break;
                    case Q.CLOCKWISE:
                      a = G(r);
                      break;
                    case Q.COUNTERCLOCKWISE:
                      a = G(r, !0);
                      break;
                    default:
                      a = e.behavior;
                  }
                  return (
                    a.forEach(function(p, s) {
                      if (r !== p || a.length === s + 1) return t;
                      (r = t.placement.split("-")[0]), (o = M(r));
                      var c = t.offsets.popper,
                        f = t.offsets.reference,
                        l = Math.floor,
                        u =
                          ("left" === r && l(c.right) > l(f.left)) ||
                          ("right" === r && l(c.left) < l(f.right)) ||
                          ("top" === r && l(c.bottom) > l(f.top)) ||
                          ("bottom" === r && l(c.top) < l(f.bottom)),
                        d = l(c.left) < l(n.left),
                        m = l(c.right) > l(n.right),
                        h = l(c.top) < l(n.top),
                        b = l(c.bottom) > l(n.bottom),
                        v =
                          ("left" === r && d) ||
                          ("right" === r && m) ||
                          ("top" === r && h) ||
                          ("bottom" === r && b),
                        g = -1 !== ["top", "bottom"].indexOf(r),
                        y =
                          !!e.flipVariations &&
                          ((g && "start" === i && d) ||
                            (g && "end" === i && m) ||
                            (!g && "start" === i && h) ||
                            (!g && "end" === i && b)),
                        w =
                          !!e.flipVariationsByContent &&
                          ((g && "start" === i && m) ||
                            (g && "end" === i && d) ||
                            (!g && "start" === i && b) ||
                            (!g && "end" === i && h)),
                        x = y || w;
                      (u || v || x) &&
                        ((t.flipped = !0),
                        (u || v) && (r = a[s + 1]),
                        x &&
                          (i = (function(t) {
                            return "end" === t
                              ? "start"
                              : "start" === t
                              ? "end"
                              : t;
                          })(i)),
                        (t.placement = r + (i ? "-" + i : "")),
                        (t.offsets.popper = O(
                          {},
                          t.offsets.popper,
                          F(t.instance.popper, t.offsets.reference, t.placement)
                        )),
                        (t = P(t.instance.modifiers, t, "flip")));
                    }),
                    t
                  );
                },
                behavior: "flip",
                padding: 5,
                boundariesElement: "viewport",
                flipVariations: !1,
                flipVariationsByContent: !1
              },
              inner: {
                order: 700,
                enabled: !1,
                fn: function(t) {
                  var e = t.placement,
                    n = e.split("-")[0],
                    r = t.offsets,
                    o = r.popper,
                    i = r.reference,
                    a = -1 !== ["left", "right"].indexOf(n),
                    p = -1 === ["top", "left"].indexOf(n);
                  return (
                    (o[a ? "left" : "top"] =
                      i[n] - (p ? o[a ? "width" : "height"] : 0)),
                    (t.placement = M(e)),
                    (t.offsets.popper = T(o)),
                    t
                  );
                }
              },
              hide: {
                order: 800,
                enabled: !0,
                fn: function(t) {
                  if (!$(t.instance.modifiers, "hide", "preventOverflow"))
                    return t;
                  var e = t.offsets.reference,
                    n = B(t.instance.modifiers, function(t) {
                      return "preventOverflow" === t.name;
                    }).boundaries;
                  if (
                    e.bottom < n.top ||
                    e.left > n.right ||
                    e.top > n.bottom ||
                    e.right < n.left
                  ) {
                    if (!0 === t.hide) return t;
                    (t.hide = !0), (t.attributes["x-out-of-boundaries"] = "");
                  } else {
                    if (!1 === t.hide) return t;
                    (t.hide = !1), (t.attributes["x-out-of-boundaries"] = !1);
                  }
                  return t;
                }
              },
              computeStyle: {
                order: 850,
                enabled: !0,
                fn: function(t, e) {
                  var n = e.x,
                    r = e.y,
                    o = t.offsets.popper,
                    i = B(t.instance.modifiers, function(t) {
                      return "applyStyle" === t.name;
                    }).gpuAcceleration;
                  void 0 !== i &&
                    console.warn(
                      "WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!"
                    );
                  var a = void 0 !== i ? i : e.gpuAcceleration,
                    p = m(t.instance.popper),
                    s = C(p),
                    c = { position: o.position },
                    f = (function(t, e) {
                      var n = t.offsets,
                        r = n.popper,
                        o = n.reference,
                        i = Math.round,
                        a = Math.floor,
                        p = function(t) {
                          return t;
                        },
                        s = i(o.width),
                        c = i(r.width),
                        f = -1 !== ["left", "right"].indexOf(t.placement),
                        l = -1 !== t.placement.indexOf("-"),
                        u = e ? (f || l || s % 2 == c % 2 ? i : a) : p,
                        d = e ? i : p;
                      return {
                        left: u(
                          s % 2 == 1 && c % 2 == 1 && !l && e
                            ? r.left - 1
                            : r.left
                        ),
                        top: d(r.top),
                        bottom: d(r.bottom),
                        right: u(r.right)
                      };
                    })(t, window.devicePixelRatio < 2 || !V),
                    l = "bottom" === n ? "top" : "bottom",
                    u = "right" === r ? "left" : "right",
                    d = H("transform"),
                    h = void 0,
                    b = void 0;
                  if (
                    ((b =
                      "bottom" === l
                        ? "HTML" === p.nodeName
                          ? -p.clientHeight + f.bottom
                          : -s.height + f.bottom
                        : f.top),
                    (h =
                      "right" === u
                        ? "HTML" === p.nodeName
                          ? -p.clientWidth + f.right
                          : -s.width + f.right
                        : f.left),
                    a && d)
                  )
                    (c[d] = "translate3d(" + h + "px, " + b + "px, 0)"),
                      (c[l] = 0),
                      (c[u] = 0),
                      (c.willChange = "transform");
                  else {
                    var v = "bottom" === l ? -1 : 1,
                      g = "right" === u ? -1 : 1;
                    (c[l] = b * v),
                      (c[u] = h * g),
                      (c.willChange = l + ", " + u);
                  }
                  var y = { "x-placement": t.placement };
                  return (
                    (t.attributes = O({}, y, t.attributes)),
                    (t.styles = O({}, c, t.styles)),
                    (t.arrowStyles = O({}, t.offsets.arrow, t.arrowStyles)),
                    t
                  );
                },
                gpuAcceleration: !0,
                x: "bottom",
                y: "right"
              },
              applyStyle: {
                order: 900,
                enabled: !0,
                fn: function(t) {
                  var e, n;
                  return (
                    W(t.instance.popper, t.styles),
                    (e = t.instance.popper),
                    (n = t.attributes),
                    Object.keys(n).forEach(function(t) {
                      !1 !== n[t]
                        ? e.setAttribute(t, n[t])
                        : e.removeAttribute(t);
                    }),
                    t.arrowElement &&
                      Object.keys(t.arrowStyles).length &&
                      W(t.arrowElement, t.arrowStyles),
                    t
                  );
                },
                onLoad: function(t, e, n, r, o) {
                  var i = j(o, e, t, n.positionFixed),
                    a = N(
                      n.placement,
                      i,
                      e,
                      t,
                      n.modifiers.flip.boundariesElement,
                      n.modifiers.flip.padding
                    );
                  return (
                    e.setAttribute("x-placement", a),
                    W(e, { position: n.positionFixed ? "fixed" : "absolute" }),
                    n
                  );
                },
                gpuAcceleration: void 0
              }
            }
          },
          et = (function() {
            function t(e, n) {
              var r = this,
                o =
                  arguments.length > 2 && void 0 !== arguments[2]
                    ? arguments[2]
                    : {};
              x(this, t),
                (this.scheduleUpdate = function() {
                  return requestAnimationFrame(r.update);
                }),
                (this.update = a(this.update.bind(this))),
                (this.options = O({}, t.Defaults, o)),
                (this.state = {
                  isDestroyed: !1,
                  isCreated: !1,
                  scrollParents: []
                }),
                (this.reference = e && e.jquery ? e[0] : e),
                (this.popper = n && n.jquery ? n[0] : n),
                (this.options.modifiers = {}),
                Object.keys(O({}, t.Defaults.modifiers, o.modifiers)).forEach(
                  function(e) {
                    r.options.modifiers[e] = O(
                      {},
                      t.Defaults.modifiers[e] || {},
                      o.modifiers ? o.modifiers[e] : {}
                    );
                  }
                ),
                (this.modifiers = Object.keys(this.options.modifiers)
                  .map(function(t) {
                    return O({ name: t }, r.options.modifiers[t]);
                  })
                  .sort(function(t, e) {
                    return t.order - e.order;
                  })),
                this.modifiers.forEach(function(t) {
                  t.enabled &&
                    p(t.onLoad) &&
                    t.onLoad(r.reference, r.popper, r.options, t, r.state);
                }),
                this.update();
              var i = this.options.eventsEnabled;
              i && this.enableEventListeners(), (this.state.eventsEnabled = i);
            }
            return (
              E(t, [
                {
                  key: "update",
                  value: function() {
                    return I.call(this);
                  }
                },
                {
                  key: "destroy",
                  value: function() {
                    return Y.call(this);
                  }
                },
                {
                  key: "enableEventListeners",
                  value: function() {
                    return q.call(this);
                  }
                },
                {
                  key: "disableEventListeners",
                  value: function() {
                    return z.call(this);
                  }
                }
              ]),
              t
            );
          })();
        (et.Utils = ("undefined" != typeof window ? window : t).PopperUtils),
          (et.placements = K),
          (et.Defaults = tt),
          (e.a = et);
      }.call(this, n(11)));
    },
    function(t, e, n) {
      t.exports = n(12);
    },
    function(t, e) {
      var n;
      n = (function() {
        return this;
      })();
      try {
        n = n || new Function("return this")();
      } catch (t) {
        "object" == typeof window && (n = window);
      }
      t.exports = n;
    },
    function(t, e, n) {
      "use strict";
      var r = n(0),
        o = n(1),
        i = n(14),
        a = n(7);
      function p(t) {
        var e = new i(t),
          n = o(i.prototype.request, e);
        return r.extend(n, i.prototype, e), r.extend(n, e), n;
      }
      var s = p(n(4));
      (s.Axios = i),
        (s.create = function(t) {
          return p(a(s.defaults, t));
        }),
        (s.Cancel = n(8)),
        (s.CancelToken = n(27)),
        (s.isCancel = n(3)),
        (s.all = function(t) {
          return Promise.all(t);
        }),
        (s.spread = n(28)),
        (t.exports = s),
        (t.exports.default = s);
    },
    function(t, e) {
      /*!
       * Determine if an object is a Buffer
       *
       * @author   Feross Aboukhadijeh <https://feross.org>
       * @license  MIT
       */
      t.exports = function(t) {
        return (
          null != t &&
          null != t.constructor &&
          "function" == typeof t.constructor.isBuffer &&
          t.constructor.isBuffer(t)
        );
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0),
        o = n(2),
        i = n(15),
        a = n(16),
        p = n(7);
      function s(t) {
        (this.defaults = t),
          (this.interceptors = { request: new i(), response: new i() });
      }
      (s.prototype.request = function(t) {
        "string" == typeof t
          ? ((t = arguments[1] || {}).url = arguments[0])
          : (t = t || {}),
          ((t = p(this.defaults, t)).method = t.method
            ? t.method.toLowerCase()
            : "get");
        var e = [a, void 0],
          n = Promise.resolve(t);
        for (
          this.interceptors.request.forEach(function(t) {
            e.unshift(t.fulfilled, t.rejected);
          }),
            this.interceptors.response.forEach(function(t) {
              e.push(t.fulfilled, t.rejected);
            });
          e.length;

        )
          n = n.then(e.shift(), e.shift());
        return n;
      }),
        (s.prototype.getUri = function(t) {
          return (
            (t = p(this.defaults, t)),
            o(t.url, t.params, t.paramsSerializer).replace(/^\?/, "")
          );
        }),
        r.forEach(["delete", "get", "head", "options"], function(t) {
          s.prototype[t] = function(e, n) {
            return this.request(r.merge(n || {}, { method: t, url: e }));
          };
        }),
        r.forEach(["post", "put", "patch"], function(t) {
          s.prototype[t] = function(e, n, o) {
            return this.request(
              r.merge(o || {}, { method: t, url: e, data: n })
            );
          };
        }),
        (t.exports = s);
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      function o() {
        this.handlers = [];
      }
      (o.prototype.use = function(t, e) {
        return (
          this.handlers.push({ fulfilled: t, rejected: e }),
          this.handlers.length - 1
        );
      }),
        (o.prototype.eject = function(t) {
          this.handlers[t] && (this.handlers[t] = null);
        }),
        (o.prototype.forEach = function(t) {
          r.forEach(this.handlers, function(e) {
            null !== e && t(e);
          });
        }),
        (t.exports = o);
    },
    function(t, e, n) {
      "use strict";
      var r = n(0),
        o = n(17),
        i = n(3),
        a = n(4),
        p = n(25),
        s = n(26);
      function c(t) {
        t.cancelToken && t.cancelToken.throwIfRequested();
      }
      t.exports = function(t) {
        return (
          c(t),
          t.baseURL && !p(t.url) && (t.url = s(t.baseURL, t.url)),
          (t.headers = t.headers || {}),
          (t.data = o(t.data, t.headers, t.transformRequest)),
          (t.headers = r.merge(
            t.headers.common || {},
            t.headers[t.method] || {},
            t.headers || {}
          )),
          r.forEach(
            ["delete", "get", "head", "post", "put", "patch", "common"],
            function(e) {
              delete t.headers[e];
            }
          ),
          (t.adapter || a.adapter)(t).then(
            function(e) {
              return (
                c(t), (e.data = o(e.data, e.headers, t.transformResponse)), e
              );
            },
            function(e) {
              return (
                i(e) ||
                  (c(t),
                  e &&
                    e.response &&
                    (e.response.data = o(
                      e.response.data,
                      e.response.headers,
                      t.transformResponse
                    ))),
                Promise.reject(e)
              );
            }
          )
        );
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      t.exports = function(t, e, n) {
        return (
          r.forEach(n, function(n) {
            t = n(t, e);
          }),
          t
        );
      };
    },
    function(t, e) {
      var n,
        r,
        o = (t.exports = {});
      function i() {
        throw new Error("setTimeout has not been defined");
      }
      function a() {
        throw new Error("clearTimeout has not been defined");
      }
      function p(t) {
        if (n === setTimeout) return setTimeout(t, 0);
        if ((n === i || !n) && setTimeout)
          return (n = setTimeout), setTimeout(t, 0);
        try {
          return n(t, 0);
        } catch (e) {
          try {
            return n.call(null, t, 0);
          } catch (e) {
            return n.call(this, t, 0);
          }
        }
      }
      !(function() {
        try {
          n = "function" == typeof setTimeout ? setTimeout : i;
        } catch (t) {
          n = i;
        }
        try {
          r = "function" == typeof clearTimeout ? clearTimeout : a;
        } catch (t) {
          r = a;
        }
      })();
      var s,
        c = [],
        f = !1,
        l = -1;
      function u() {
        f &&
          s &&
          ((f = !1), s.length ? (c = s.concat(c)) : (l = -1), c.length && d());
      }
      function d() {
        if (!f) {
          var t = p(u);
          f = !0;
          for (var e = c.length; e; ) {
            for (s = c, c = []; ++l < e; ) s && s[l].run();
            (l = -1), (e = c.length);
          }
          (s = null),
            (f = !1),
            (function(t) {
              if (r === clearTimeout) return clearTimeout(t);
              if ((r === a || !r) && clearTimeout)
                return (r = clearTimeout), clearTimeout(t);
              try {
                r(t);
              } catch (e) {
                try {
                  return r.call(null, t);
                } catch (e) {
                  return r.call(this, t);
                }
              }
            })(t);
        }
      }
      function m(t, e) {
        (this.fun = t), (this.array = e);
      }
      function h() {}
      (o.nextTick = function(t) {
        var e = new Array(arguments.length - 1);
        if (arguments.length > 1)
          for (var n = 1; n < arguments.length; n++) e[n - 1] = arguments[n];
        c.push(new m(t, e)), 1 !== c.length || f || p(d);
      }),
        (m.prototype.run = function() {
          this.fun.apply(null, this.array);
        }),
        (o.title = "browser"),
        (o.browser = !0),
        (o.env = {}),
        (o.argv = []),
        (o.version = ""),
        (o.versions = {}),
        (o.on = h),
        (o.addListener = h),
        (o.once = h),
        (o.off = h),
        (o.removeListener = h),
        (o.removeAllListeners = h),
        (o.emit = h),
        (o.prependListener = h),
        (o.prependOnceListener = h),
        (o.listeners = function(t) {
          return [];
        }),
        (o.binding = function(t) {
          throw new Error("process.binding is not supported");
        }),
        (o.cwd = function() {
          return "/";
        }),
        (o.chdir = function(t) {
          throw new Error("process.chdir is not supported");
        }),
        (o.umask = function() {
          return 0;
        });
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      t.exports = function(t, e) {
        r.forEach(t, function(n, r) {
          r !== e &&
            r.toUpperCase() === e.toUpperCase() &&
            ((t[e] = n), delete t[r]);
        });
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(6);
      t.exports = function(t, e, n) {
        var o = n.config.validateStatus;
        !o || o(n.status)
          ? t(n)
          : e(
              r(
                "Request failed with status code " + n.status,
                n.config,
                null,
                n.request,
                n
              )
            );
      };
    },
    function(t, e, n) {
      "use strict";
      t.exports = function(t, e, n, r, o) {
        return (
          (t.config = e),
          n && (t.code = n),
          (t.request = r),
          (t.response = o),
          (t.isAxiosError = !0),
          (t.toJSON = function() {
            return {
              message: this.message,
              name: this.name,
              description: this.description,
              number: this.number,
              fileName: this.fileName,
              lineNumber: this.lineNumber,
              columnNumber: this.columnNumber,
              stack: this.stack,
              config: this.config,
              code: this.code
            };
          }),
          t
        );
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0),
        o = [
          "age",
          "authorization",
          "content-length",
          "content-type",
          "etag",
          "expires",
          "from",
          "host",
          "if-modified-since",
          "if-unmodified-since",
          "last-modified",
          "location",
          "max-forwards",
          "proxy-authorization",
          "referer",
          "retry-after",
          "user-agent"
        ];
      t.exports = function(t) {
        var e,
          n,
          i,
          a = {};
        return t
          ? (r.forEach(t.split("\n"), function(t) {
              if (
                ((i = t.indexOf(":")),
                (e = r.trim(t.substr(0, i)).toLowerCase()),
                (n = r.trim(t.substr(i + 1))),
                e)
              ) {
                if (a[e] && o.indexOf(e) >= 0) return;
                a[e] =
                  "set-cookie" === e
                    ? (a[e] ? a[e] : []).concat([n])
                    : a[e]
                    ? a[e] + ", " + n
                    : n;
              }
            }),
            a)
          : a;
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      t.exports = r.isStandardBrowserEnv()
        ? (function() {
            var t,
              e = /(msie|trident)/i.test(navigator.userAgent),
              n = document.createElement("a");
            function o(t) {
              var r = t;
              return (
                e && (n.setAttribute("href", r), (r = n.href)),
                n.setAttribute("href", r),
                {
                  href: n.href,
                  protocol: n.protocol ? n.protocol.replace(/:$/, "") : "",
                  host: n.host,
                  search: n.search ? n.search.replace(/^\?/, "") : "",
                  hash: n.hash ? n.hash.replace(/^#/, "") : "",
                  hostname: n.hostname,
                  port: n.port,
                  pathname:
                    "/" === n.pathname.charAt(0) ? n.pathname : "/" + n.pathname
                }
              );
            }
            return (
              (t = o(window.location.href)),
              function(e) {
                var n = r.isString(e) ? o(e) : e;
                return n.protocol === t.protocol && n.host === t.host;
              }
            );
          })()
        : function() {
            return !0;
          };
    },
    function(t, e, n) {
      "use strict";
      var r = n(0);
      t.exports = r.isStandardBrowserEnv()
        ? {
            write: function(t, e, n, o, i, a) {
              var p = [];
              p.push(t + "=" + encodeURIComponent(e)),
                r.isNumber(n) && p.push("expires=" + new Date(n).toGMTString()),
                r.isString(o) && p.push("path=" + o),
                r.isString(i) && p.push("domain=" + i),
                !0 === a && p.push("secure"),
                (document.cookie = p.join("; "));
            },
            read: function(t) {
              var e = document.cookie.match(
                new RegExp("(^|;\\s*)(" + t + ")=([^;]*)")
              );
              return e ? decodeURIComponent(e[3]) : null;
            },
            remove: function(t) {
              this.write(t, "", Date.now() - 864e5);
            }
          }
        : {
            write: function() {},
            read: function() {
              return null;
            },
            remove: function() {}
          };
    },
    function(t, e, n) {
      "use strict";
      t.exports = function(t) {
        return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(t);
      };
    },
    function(t, e, n) {
      "use strict";
      t.exports = function(t, e) {
        return e ? t.replace(/\/+$/, "") + "/" + e.replace(/^\/+/, "") : t;
      };
    },
    function(t, e, n) {
      "use strict";
      var r = n(8);
      function o(t) {
        if ("function" != typeof t)
          throw new TypeError("executor must be a function.");
        var e;
        this.promise = new Promise(function(t) {
          e = t;
        });
        var n = this;
        t(function(t) {
          n.reason || ((n.reason = new r(t)), e(n.reason));
        });
      }
      (o.prototype.throwIfRequested = function() {
        if (this.reason) throw this.reason;
      }),
        (o.source = function() {
          var t;
          return {
            token: new o(function(e) {
              t = e;
            }),
            cancel: t
          };
        }),
        (t.exports = o);
    },
    function(t, e, n) {
      "use strict";
      t.exports = function(t) {
        return function(e) {
          return t.apply(null, e);
        };
      };
    },
    function(t, e, n) {
      "use strict";
      n.r(e);
      var r = n(9);
      /**!
       * tippy.js v4.3.5
       * (c) 2017-2019 atomiks
       * MIT License
       */ function o() {
        return (o =
          Object.assign ||
          function(t) {
            for (var e = 1; e < arguments.length; e++) {
              var n = arguments[e];
              for (var r in n)
                Object.prototype.hasOwnProperty.call(n, r) && (t[r] = n[r]);
            }
            return t;
          }).apply(this, arguments);
      }
      var i = "undefined" != typeof window && "undefined" != typeof document,
        a = i ? navigator.userAgent : "",
        p = /MSIE |Trident\//.test(a),
        s = /UCBrowser\//.test(a),
        c =
          i && /iPhone|iPad|iPod/.test(navigator.platform) && !window.MSStream,
        f = {
          a11y: !0,
          allowHTML: !0,
          animateFill: !0,
          animation: "shift-away",
          appendTo: function() {
            return document.body;
          },
          aria: "describedby",
          arrow: !1,
          arrowType: "sharp",
          boundary: "scrollParent",
          content: "",
          delay: 0,
          distance: 10,
          duration: [325, 275],
          flip: !0,
          flipBehavior: "flip",
          flipOnUpdate: !1,
          followCursor: !1,
          hideOnClick: !0,
          ignoreAttributes: !1,
          inertia: !1,
          interactive: !1,
          interactiveBorder: 2,
          interactiveDebounce: 0,
          lazy: !0,
          maxWidth: 350,
          multiple: !1,
          offset: 0,
          onHidden: function() {},
          onHide: function() {},
          onMount: function() {},
          onShow: function() {},
          onShown: function() {},
          onTrigger: function() {},
          placement: "top",
          popperOptions: {},
          role: "tooltip",
          showOnInit: !1,
          size: "regular",
          sticky: !1,
          target: "",
          theme: "dark",
          touch: !0,
          touchHold: !1,
          trigger: "mouseenter focus",
          triggerTarget: null,
          updateDuration: 0,
          wait: null,
          zIndex: 9999
        },
        l = [
          "arrow",
          "arrowType",
          "boundary",
          "distance",
          "flip",
          "flipBehavior",
          "flipOnUpdate",
          "offset",
          "placement",
          "popperOptions"
        ],
        u = i ? Element.prototype : {},
        d =
          u.matches ||
          u.matchesSelector ||
          u.webkitMatchesSelector ||
          u.mozMatchesSelector ||
          u.msMatchesSelector;
      function m(t) {
        return [].slice.call(t);
      }
      function h(t, e) {
        return b(t, function(t) {
          return d.call(t, e);
        });
      }
      function b(t, e) {
        for (; t; ) {
          if (e(t)) return t;
          t = t.parentElement;
        }
        return null;
      }
      var v = { passive: !0 },
        g = 4,
        y = "x-placement",
        w = "x-out-of-boundaries",
        x = "tippy-iOS",
        E = "tippy-active",
        k = "tippy-popper",
        O = "tippy-tooltip",
        T = "tippy-content",
        C = "tippy-backdrop",
        A = "tippy-arrow",
        L = "tippy-roundarrow",
        S = ".".concat(k),
        N = ".".concat(O),
        j = ".".concat(T),
        D = ".".concat(C),
        M = ".".concat(A),
        F = ".".concat(L),
        B = !1;
      function P() {
        B ||
          ((B = !0),
          c && document.body.classList.add(x),
          window.performance && document.addEventListener("mousemove", R));
      }
      var I = 0;
      function R() {
        var t = performance.now();
        t - I < 20 &&
          ((B = !1),
          document.removeEventListener("mousemove", R),
          c || document.body.classList.remove(x)),
          (I = t);
      }
      function H() {
        var t = document.activeElement;
        t && t.blur && t._tippy && t.blur();
      }
      var Y = Object.keys(f);
      function U(t, e) {
        return {}.hasOwnProperty.call(t, e);
      }
      function X(t, e, n) {
        if (Array.isArray(t)) {
          var r = t[e];
          return null == r ? n : r;
        }
        return t;
      }
      function q(t, e) {
        return 0 === e
          ? t
          : function(r) {
              clearTimeout(n),
                (n = setTimeout(function() {
                  t(r);
                }, e));
            };
        var n;
      }
      function z(t, e) {
        return t && t.modifiers && t.modifiers[e];
      }
      function _(t, e) {
        return t.indexOf(e) > -1;
      }
      function W(t) {
        return t instanceof Element;
      }
      function V(t) {
        return !(!t || !U(t, "isVirtual")) || W(t);
      }
      function $(t, e) {
        return "function" == typeof t ? t.apply(null, e) : t;
      }
      function K(t, e) {
        t.filter(function(t) {
          return "flip" === t.name;
        })[0].enabled = e;
      }
      function J() {
        return document.createElement("div");
      }
      function G(t, e) {
        t.forEach(function(t) {
          t && (t.style.transitionDuration = "".concat(e, "ms"));
        });
      }
      function Q(t, e) {
        t.forEach(function(t) {
          t && t.setAttribute("data-state", e);
        });
      }
      function Z(t, e) {
        var n = o(
          {},
          e,
          { content: $(e.content, [t]) },
          e.ignoreAttributes
            ? {}
            : (function(t) {
                return Y.reduce(function(e, n) {
                  var r = (
                    t.getAttribute("data-tippy-".concat(n)) || ""
                  ).trim();
                  if (!r) return e;
                  if ("content" === n) e[n] = r;
                  else
                    try {
                      e[n] = JSON.parse(r);
                    } catch (t) {
                      e[n] = r;
                    }
                  return e;
                }, {});
              })(t)
        );
        return (n.arrow || s) && (n.animateFill = !1), n;
      }
      function tt(t, e) {
        Object.keys(t).forEach(function(t) {
          if (!U(e, t))
            throw new Error("[tippy]: `".concat(t, "` is not a valid option"));
        });
      }
      function et(t, e) {
        t.innerHTML = W(e) ? e.innerHTML : e;
      }
      function nt(t, e) {
        if (W(e.content)) et(t, ""), t.appendChild(e.content);
        else if ("function" != typeof e.content) {
          t[e.allowHTML ? "innerHTML" : "textContent"] = e.content;
        }
      }
      function rt(t) {
        return {
          tooltip: t.querySelector(N),
          backdrop: t.querySelector(D),
          content: t.querySelector(j),
          arrow: t.querySelector(M) || t.querySelector(F)
        };
      }
      function ot(t) {
        t.setAttribute("data-inertia", "");
      }
      function it(t) {
        var e = J();
        return (
          "round" === t
            ? ((e.className = L),
              et(
                e,
                '<svg viewBox="0 0 18 7" xmlns="http://www.w3.org/2000/svg"><path d="M0 7s2.021-.015 5.253-4.218C6.584 1.051 7.797.007 9 0c1.203-.007 2.416 1.035 3.761 2.782C16.012 7.005 18 7 18 7H0z"/></svg>'
              ))
            : (e.className = A),
          e
        );
      }
      function at() {
        var t = J();
        return (t.className = C), t.setAttribute("data-state", "hidden"), t;
      }
      function pt(t, e) {
        t.setAttribute("tabindex", "-1"),
          e.setAttribute("data-interactive", "");
      }
      function st(t, e, n) {
        var r =
          s && void 0 !== document.body.style.webkitTransition
            ? "webkitTransitionEnd"
            : "transitionend";
        t[e + "EventListener"](r, n);
      }
      function ct(t) {
        var e = t.getAttribute(y);
        return e ? e.split("-")[0] : "";
      }
      function ft(t, e, n) {
        n.split(" ").forEach(function(n) {
          t.classList[e](n + "-theme");
        });
      }
      var lt = 1,
        ut = [];
      function dt(t, e) {
        var n,
          i,
          a,
          s,
          c,
          u = Z(t, e);
        if (!u.multiple && t._tippy) return null;
        var x,
          C,
          A,
          L,
          N,
          j = !1,
          D = !1,
          M = !1,
          F = !1,
          P = [],
          I = q(Ct, u.interactiveDebounce),
          R = lt++,
          H = (function(t, e) {
            var n = J();
            (n.className = k),
              (n.id = "tippy-".concat(t)),
              (n.style.zIndex = "" + e.zIndex),
              (n.style.position = "absolute"),
              (n.style.top = "0"),
              (n.style.left = "0"),
              e.role && n.setAttribute("role", e.role);
            var r = J();
            (r.className = O),
              (r.style.maxWidth =
                e.maxWidth + ("number" == typeof e.maxWidth ? "px" : "")),
              r.setAttribute("data-size", e.size),
              r.setAttribute("data-animation", e.animation),
              r.setAttribute("data-state", "hidden"),
              ft(r, "add", e.theme);
            var o = J();
            return (
              (o.className = T),
              o.setAttribute("data-state", "hidden"),
              e.interactive && pt(n, r),
              e.arrow && r.appendChild(it(e.arrowType)),
              e.animateFill &&
                (r.appendChild(at()), r.setAttribute("data-animatefill", "")),
              e.inertia && ot(r),
              nt(o, e),
              r.appendChild(o),
              n.appendChild(r),
              n
            );
          })(R, u),
          Y = rt(H),
          V = {
            id: R,
            reference: t,
            popper: H,
            popperChildren: Y,
            popperInstance: null,
            props: u,
            state: {
              isEnabled: !0,
              isVisible: !1,
              isDestroyed: !1,
              isMounted: !1,
              isShown: !1
            },
            clearDelayTimeouts: It,
            set: Rt,
            setContent: function(t) {
              Rt({ content: t });
            },
            show: Ht,
            hide: Yt,
            enable: function() {
              V.state.isEnabled = !0;
            },
            disable: function() {
              V.state.isEnabled = !1;
            },
            destroy: function(e) {
              if (V.state.isDestroyed) return;
              (D = !0), V.state.isMounted && Yt(0);
              kt(), delete t._tippy;
              var n = V.props.target;
              n &&
                e &&
                W(t) &&
                m(t.querySelectorAll(n)).forEach(function(t) {
                  t._tippy && t._tippy.destroy();
                });
              V.popperInstance && V.popperInstance.destroy();
              (D = !1), (V.state.isDestroyed = !0);
            }
          };
        return (
          (t._tippy = V),
          (H._tippy = V),
          Et(),
          u.lazy || Mt(),
          u.showOnInit && Ft(),
          !u.a11y ||
            u.target ||
            (!W((N = ht())) ||
              (d.call(
                N,
                "a[href],area[href],button,details,input,textarea,select,iframe,[tabindex]"
              ) &&
                !N.hasAttribute("disabled"))) ||
            ht().setAttribute("tabindex", "0"),
          H.addEventListener("mouseenter", function(t) {
            V.props.interactive &&
              V.state.isVisible &&
              "mouseenter" === n &&
              Ft(t, !0);
          }),
          H.addEventListener("mouseleave", function() {
            V.props.interactive &&
              "mouseenter" === n &&
              document.addEventListener("mousemove", I);
          }),
          V
        );
        function et() {
          document.removeEventListener("mousemove", Ot);
        }
        function mt() {
          document.body.removeEventListener("mouseleave", Bt),
            document.removeEventListener("mousemove", I),
            (ut = ut.filter(function(t) {
              return t !== I;
            }));
        }
        function ht() {
          return V.props.triggerTarget || t;
        }
        function bt() {
          document.addEventListener("click", Pt, !0);
        }
        function vt() {
          document.removeEventListener("click", Pt, !0);
        }
        function gt() {
          return [
            V.popperChildren.tooltip,
            V.popperChildren.backdrop,
            V.popperChildren.content
          ];
        }
        function yt() {
          var t = V.props.followCursor;
          return (t && "focus" !== n) || (B && "initial" === t);
        }
        function wt(t, e) {
          var n = V.popperChildren.tooltip;
          function r(t) {
            t.target === n && (st(n, "remove", r), e());
          }
          if (0 === t) return e();
          st(n, "remove", A), st(n, "add", r), (A = r);
        }
        function xt(t, e) {
          var n =
            arguments.length > 2 && void 0 !== arguments[2] && arguments[2];
          ht().addEventListener(t, e, n),
            P.push({ eventType: t, handler: e, options: n });
        }
        function Et() {
          V.props.touchHold &&
            !V.props.target &&
            (xt("touchstart", Tt, v), xt("touchend", At, v)),
            V.props.trigger
              .trim()
              .split(" ")
              .forEach(function(t) {
                if ("manual" !== t)
                  if (V.props.target)
                    switch (t) {
                      case "mouseenter":
                        xt("mouseover", St), xt("mouseout", Nt);
                        break;
                      case "focus":
                        xt("focusin", St), xt("focusout", Nt);
                        break;
                      case "click":
                        xt(t, St);
                    }
                  else
                    switch ((xt(t, Tt), t)) {
                      case "mouseenter":
                        xt("mouseleave", At);
                        break;
                      case "focus":
                        xt(p ? "focusout" : "blur", Lt);
                    }
              });
        }
        function kt() {
          P.forEach(function(t) {
            var e = t.eventType,
              n = t.handler,
              r = t.options;
            ht().removeEventListener(e, n, r);
          }),
            (P = []);
        }
        function Ot(e) {
          var n = (i = e),
            r = n.clientX,
            a = n.clientY;
          if (L) {
            var p = b(e.target, function(e) {
                return e === t;
              }),
              s = t.getBoundingClientRect(),
              c = V.props.followCursor,
              f = "horizontal" === c,
              l = "vertical" === c,
              u = _(["top", "bottom"], ct(H)),
              d = H.getAttribute(y),
              m = !!d && !!d.split("-")[1],
              h = u ? H.offsetWidth : H.offsetHeight,
              v = h / 2,
              g = u ? 0 : m ? h : v,
              w = u ? (m ? h : v) : 0;
            (!p && V.props.interactive) ||
              ((V.popperInstance.reference = o({}, V.popperInstance.reference, {
                referenceNode: t,
                clientWidth: 0,
                clientHeight: 0,
                getBoundingClientRect: function() {
                  return {
                    width: u ? h : 0,
                    height: u ? 0 : h,
                    top: (f ? s.top : a) - g,
                    bottom: (f ? s.bottom : a) + g,
                    left: (l ? s.left : r) - w,
                    right: (l ? s.right : r) + w
                  };
                }
              })),
              V.popperInstance.update()),
              "initial" === c && V.state.isVisible && et();
          }
        }
        function Tt(t) {
          V.state.isEnabled &&
            !jt(t) &&
            (V.state.isVisible ||
              ((n = t.type),
              t instanceof MouseEvent &&
                ((i = t),
                ut.forEach(function(e) {
                  return e(t);
                }))),
            "click" === t.type &&
            !1 !== V.props.hideOnClick &&
            V.state.isVisible
              ? Bt()
              : Ft(t));
        }
        function Ct(e) {
          var n = h(e.target, S) === H,
            r = b(e.target, function(e) {
              return e === t;
            });
          n ||
            r ||
            ((function(t, e, n, r) {
              if (!t) return !0;
              var o = n.clientX,
                i = n.clientY,
                a = r.interactiveBorder,
                p = r.distance,
                s = e.top - i > ("top" === t ? a + p : a),
                c = i - e.bottom > ("bottom" === t ? a + p : a),
                f = e.left - o > ("left" === t ? a + p : a),
                l = o - e.right > ("right" === t ? a + p : a);
              return s || c || f || l;
            })(ct(H), H.getBoundingClientRect(), e, V.props) &&
              (mt(), Bt()));
        }
        function At(t) {
          if (!jt(t))
            return V.props.interactive
              ? (document.body.addEventListener("mouseleave", Bt),
                document.addEventListener("mousemove", I),
                void ut.push(I))
              : void Bt();
        }
        function Lt(t) {
          t.target === ht() &&
            ((V.props.interactive &&
              t.relatedTarget &&
              H.contains(t.relatedTarget)) ||
              Bt());
        }
        function St(t) {
          h(t.target, V.props.target) && Ft(t);
        }
        function Nt(t) {
          h(t.target, V.props.target) && Bt();
        }
        function jt(t) {
          var e = "ontouchstart" in window,
            n = _(t.type, "touch"),
            r = V.props.touchHold;
          return (e && B && r && !n) || (B && !r && n);
        }
        function Dt() {
          !F &&
            C &&
            ((F = !0),
            (function(t) {
              t.offsetHeight;
            })(H),
            C());
        }
        function Mt() {
          var e = V.props.popperOptions,
            n = V.popperChildren,
            i = n.tooltip,
            a = n.arrow,
            p = z(e, "preventOverflow");
          function s(t) {
            V.props.flip &&
              !V.props.flipOnUpdate &&
              (t.flipped && (V.popperInstance.options.placement = t.placement),
              K(V.popperInstance.modifiers, !1)),
              i.setAttribute(y, t.placement),
              !1 !== t.attributes[w]
                ? i.setAttribute(w, "")
                : i.removeAttribute(w),
              x &&
                x !== t.placement &&
                M &&
                ((i.style.transition = "none"),
                requestAnimationFrame(function() {
                  i.style.transition = "";
                })),
              (x = t.placement),
              (M = V.state.isVisible);
            var e = ct(H),
              n = i.style;
            (n.top = n.bottom = n.left = n.right = ""),
              (n[e] = -(V.props.distance - 10) + "px");
            var r = p && void 0 !== p.padding ? p.padding : g,
              a = "number" == typeof r,
              s = o(
                {
                  top: a ? r : r.top,
                  bottom: a ? r : r.bottom,
                  left: a ? r : r.left,
                  right: a ? r : r.right
                },
                !a && r
              );
            (s[e] = a ? r + V.props.distance : (r[e] || 0) + V.props.distance),
              (V.popperInstance.modifiers.filter(function(t) {
                return "preventOverflow" === t.name;
              })[0].padding = s),
              (L = s);
          }
          var c = o({ eventsEnabled: !1, placement: V.props.placement }, e, {
            modifiers: o({}, e ? e.modifiers : {}, {
              preventOverflow: o(
                { boundariesElement: V.props.boundary, padding: g },
                p
              ),
              arrow: o({ element: a, enabled: !!a }, z(e, "arrow")),
              flip: o(
                {
                  enabled: V.props.flip,
                  padding: V.props.distance + g,
                  behavior: V.props.flipBehavior
                },
                z(e, "flip")
              ),
              offset: o({ offset: V.props.offset }, z(e, "offset"))
            }),
            onCreate: function(t) {
              s(t), Dt(), e && e.onCreate && e.onCreate(t);
            },
            onUpdate: function(t) {
              s(t), Dt(), e && e.onUpdate && e.onUpdate(t);
            }
          });
          V.popperInstance = new r.a(t, H, c);
        }
        function Ft(t, n) {
          if ((It(), !V.state.isVisible)) {
            if (V.props.target)
              return (function(t) {
                if (t) {
                  var n = h(t.target, V.props.target);
                  n &&
                    !n._tippy &&
                    dt(
                      n,
                      o({}, V.props, {
                        content: $(e.content, [n]),
                        appendTo: e.appendTo,
                        target: "",
                        showOnInit: !0
                      })
                    );
                }
              })(t);
            if (((j = !0), t && !n && V.props.onTrigger(V, t), V.props.wait))
              return V.props.wait(V, t);
            yt() &&
              !V.state.isMounted &&
              (V.popperInstance || Mt(),
              document.addEventListener("mousemove", Ot)),
              bt();
            var r = X(V.props.delay, 0, f.delay);
            r
              ? (a = setTimeout(function() {
                  Ht();
                }, r))
              : Ht();
          }
        }
        function Bt() {
          if ((It(), !V.state.isVisible)) return et(), void vt();
          j = !1;
          var t = X(V.props.delay, 1, f.delay);
          t
            ? (s = setTimeout(function() {
                V.state.isVisible && Yt();
              }, t))
            : (c = requestAnimationFrame(function() {
                Yt();
              }));
        }
        function Pt(t) {
          if (!V.props.interactive || !H.contains(t.target)) {
            if (ht().contains(t.target)) {
              if (B) return;
              if (V.state.isVisible && _(V.props.trigger, "click")) return;
            }
            !0 === V.props.hideOnClick && (It(), Yt());
          }
        }
        function It() {
          clearTimeout(a), clearTimeout(s), cancelAnimationFrame(c);
        }
        function Rt(e) {
          tt((e = e || {}), f), kt();
          var n = V.props,
            r = Z(t, o({}, V.props, {}, e, { ignoreAttributes: !0 }));
          (r.ignoreAttributes = U(e, "ignoreAttributes")
            ? e.ignoreAttributes || !1
            : n.ignoreAttributes),
            (V.props = r),
            Et(),
            mt(),
            (I = q(Ct, r.interactiveDebounce)),
            (function(t, e, n) {
              var r = rt(t),
                o = r.tooltip,
                i = r.content,
                a = r.backdrop,
                p = r.arrow;
              (t.style.zIndex = "" + n.zIndex),
                o.setAttribute("data-size", n.size),
                o.setAttribute("data-animation", n.animation),
                (o.style.maxWidth =
                  n.maxWidth + ("number" == typeof n.maxWidth ? "px" : "")),
                n.role
                  ? t.setAttribute("role", n.role)
                  : t.removeAttribute("role"),
                e.content !== n.content && nt(i, n),
                !e.animateFill && n.animateFill
                  ? (o.appendChild(at()),
                    o.setAttribute("data-animatefill", ""))
                  : e.animateFill &&
                    !n.animateFill &&
                    (o.removeChild(a), o.removeAttribute("data-animatefill")),
                !e.arrow && n.arrow
                  ? o.appendChild(it(n.arrowType))
                  : e.arrow && !n.arrow && o.removeChild(p),
                e.arrow &&
                  n.arrow &&
                  e.arrowType !== n.arrowType &&
                  o.replaceChild(it(n.arrowType), p),
                !e.interactive && n.interactive
                  ? pt(t, o)
                  : e.interactive &&
                    !n.interactive &&
                    (function(t, e) {
                      t.removeAttribute("tabindex"),
                        e.removeAttribute("data-interactive");
                    })(t, o),
                !e.inertia && n.inertia
                  ? ot(o)
                  : e.inertia &&
                    !n.inertia &&
                    (function(t) {
                      t.removeAttribute("data-inertia");
                    })(o),
                e.theme !== n.theme &&
                  (ft(o, "remove", e.theme), ft(o, "add", n.theme));
            })(H, n, r),
            (V.popperChildren = rt(H)),
            V.popperInstance &&
              (l.some(function(t) {
                return U(e, t) && e[t] !== n[t];
              })
                ? (V.popperInstance.destroy(),
                  Mt(),
                  V.state.isVisible && V.popperInstance.enableEventListeners(),
                  V.props.followCursor && i && Ot(i))
                : V.popperInstance.update());
        }
        function Ht() {
          var e =
            arguments.length > 0 && void 0 !== arguments[0]
              ? arguments[0]
              : X(V.props.duration, 0, f.duration[1]);
          if (
            !V.state.isDestroyed &&
            V.state.isEnabled &&
            (!B || V.props.touch) &&
            !ht().hasAttribute("disabled") &&
            !1 !== V.props.onShow(V)
          ) {
            bt(),
              (H.style.visibility = "visible"),
              (V.state.isVisible = !0),
              V.props.interactive && ht().classList.add(E);
            var n = gt();
            G(n.concat(H), 0),
              (C = function() {
                if (V.state.isVisible) {
                  var r = yt();
                  r && i ? Ot(i) : r || V.popperInstance.update(),
                    V.popperChildren.backdrop &&
                      (V.popperChildren.content.style.transitionDelay =
                        Math.round(e / 12) + "ms"),
                    V.props.sticky &&
                      (function() {
                        G([H], p ? 0 : V.props.updateDuration);
                        var e = t.getBoundingClientRect();
                        !(function n() {
                          var r = t.getBoundingClientRect();
                          (e.top === r.top &&
                            e.right === r.right &&
                            e.bottom === r.bottom &&
                            e.left === r.left) ||
                            V.popperInstance.scheduleUpdate(),
                            (e = r),
                            V.state.isMounted && requestAnimationFrame(n);
                        })();
                      })(),
                    G([H], V.props.updateDuration),
                    G(n, e),
                    Q(n, "visible"),
                    (function(t, e) {
                      wt(t, e);
                    })(e, function() {
                      V.props.aria &&
                        ht().setAttribute("aria-".concat(V.props.aria), H.id),
                        V.props.onShown(V),
                        (V.state.isShown = !0);
                    });
                }
              }),
              (function() {
                F = !1;
                var e = yt();
                V.popperInstance
                  ? (K(V.popperInstance.modifiers, V.props.flip),
                    e ||
                      ((V.popperInstance.reference = t),
                      V.popperInstance.enableEventListeners()),
                    V.popperInstance.scheduleUpdate())
                  : (Mt(), e || V.popperInstance.enableEventListeners());
                var n = V.props.appendTo,
                  r = "parent" === n ? t.parentNode : $(n, [t]);
                r.contains(H) ||
                  (r.appendChild(H),
                  V.props.onMount(V),
                  (V.state.isMounted = !0));
              })();
          }
        }
        function Yt() {
          var t =
            arguments.length > 0 && void 0 !== arguments[0]
              ? arguments[0]
              : X(V.props.duration, 1, f.duration[1]);
          if (
            !V.state.isDestroyed &&
            (V.state.isEnabled || D) &&
            (!1 !== V.props.onHide(V) || D)
          ) {
            vt(),
              (H.style.visibility = "hidden"),
              (V.state.isVisible = !1),
              (V.state.isShown = !1),
              (M = !1),
              V.props.interactive && ht().classList.remove(E);
            var e = gt();
            G(e, t),
              Q(e, "hidden"),
              (function(t, e) {
                wt(t, function() {
                  !V.state.isVisible &&
                    H.parentNode &&
                    H.parentNode.contains(H) &&
                    e();
                });
              })(t, function() {
                j || et(),
                  V.props.aria &&
                    ht().removeAttribute("aria-".concat(V.props.aria)),
                  V.popperInstance.disableEventListeners(),
                  (V.popperInstance.options.placement = V.props.placement),
                  H.parentNode.removeChild(H),
                  V.props.onHidden(V),
                  (V.state.isMounted = !1);
              });
          }
        }
      }
      var mt = !1;
      function ht(t, e) {
        tt(e || {}, f),
          mt ||
            (document.addEventListener("touchstart", P, v),
            window.addEventListener("blur", H),
            (mt = !0));
        var n,
          r = o({}, f, {}, e);
        (n = t),
          "[object Object]" !== {}.toString.call(n) ||
            n.addEventListener ||
            (function(t) {
              var e = {
                isVirtual: !0,
                attributes: t.attributes || {},
                contains: function() {},
                setAttribute: function(e, n) {
                  t.attributes[e] = n;
                },
                getAttribute: function(e) {
                  return t.attributes[e];
                },
                removeAttribute: function(e) {
                  delete t.attributes[e];
                },
                hasAttribute: function(e) {
                  return e in t.attributes;
                },
                addEventListener: function() {},
                removeEventListener: function() {},
                classList: {
                  classNames: {},
                  add: function(e) {
                    t.classList.classNames[e] = !0;
                  },
                  remove: function(e) {
                    delete t.classList.classNames[e];
                  },
                  contains: function(e) {
                    return e in t.classList.classNames;
                  }
                }
              };
              for (var n in e) t[n] = e[n];
            })(t);
        var i = (function(t) {
          if (V(t)) return [t];
          if (t instanceof NodeList) return m(t);
          if (Array.isArray(t)) return t;
          try {
            return m(document.querySelectorAll(t));
          } catch (t) {
            return [];
          }
        })(t).reduce(function(t, e) {
          var n = e && dt(e, r);
          return n && t.push(n), t;
        }, []);
        return V(t) ? i[0] : i;
      }
      (ht.version = "4.3.5"),
        (ht.defaults = f),
        (ht.setDefaults = function(t) {
          Object.keys(t).forEach(function(e) {
            f[e] = t[e];
          });
        }),
        (ht.hideAll = function() {
          var t =
              arguments.length > 0 && void 0 !== arguments[0]
                ? arguments[0]
                : {},
            e = t.exclude,
            n = t.duration;
          m(document.querySelectorAll(S)).forEach(function(t) {
            var r,
              o = t._tippy;
            if (o) {
              var i = !1;
              e &&
                (i =
                  (r = e)._tippy && !d.call(r, S)
                    ? o.reference === e
                    : t === e.popper),
                i || o.hide(n);
            }
          });
        }),
        (ht.group = function(t) {
          var e =
              arguments.length > 1 && void 0 !== arguments[1]
                ? arguments[1]
                : {},
            n = e.delay,
            r = void 0 === n ? t[0].props.delay : n,
            i = e.duration,
            a = void 0 === i ? 0 : i,
            p = !1;
          function s(t) {
            (p = t), u();
          }
          function c(e) {
            e._originalProps.onShow(e),
              t.forEach(function(t) {
                t.set({ duration: a }), t.state.isVisible && t.hide();
              }),
              s(!0);
          }
          function f(t) {
            t._originalProps.onHide(t), s(!1);
          }
          function l(t) {
            t._originalProps.onShown(t),
              t.set({ duration: t._originalProps.duration });
          }
          function u() {
            t.forEach(function(t) {
              t.set({
                onShow: c,
                onShown: l,
                onHide: f,
                delay: p ? [0, Array.isArray(r) ? r[1] : r] : r,
                duration: p ? a : t._originalProps.duration
              });
            });
          }
          t.forEach(function(t) {
            t._originalProps
              ? t.set(t._originalProps)
              : (t._originalProps = o({}, t.props));
          }),
            u();
        }),
        i &&
          setTimeout(function() {
            m(document.querySelectorAll("[data-tippy]")).forEach(function(t) {
              var e = t.getAttribute("data-tippy");
              e && ht(t, { content: e });
            });
          }),
        (function(t) {
          if (i) {
            var e = document.createElement("style");
            (e.type = "text/css"),
              (e.textContent = t),
              e.setAttribute("data-tippy-stylesheet", "");
            var n = document.head,
              r = n.querySelector("style,link");
            r ? n.insertBefore(e, r) : n.appendChild(e);
          }
        })(
          '.tippy-iOS{cursor:pointer!important;-webkit-tap-highlight-color:transparent}.tippy-popper{transition-timing-function:cubic-bezier(.165,.84,.44,1);max-width:calc(100% - 8px);pointer-events:none;outline:0}.tippy-popper[x-placement^=top] .tippy-backdrop{border-radius:40% 40% 0 0}.tippy-popper[x-placement^=top] .tippy-roundarrow{bottom:-7px;bottom:-6.5px;-webkit-transform-origin:50% 0;transform-origin:50% 0;margin:0 3px}.tippy-popper[x-placement^=top] .tippy-roundarrow svg{position:absolute;left:0;-webkit-transform:rotate(180deg);transform:rotate(180deg)}.tippy-popper[x-placement^=top] .tippy-arrow{border-top:8px solid #333;border-right:8px solid transparent;border-left:8px solid transparent;bottom:-7px;margin:0 3px;-webkit-transform-origin:50% 0;transform-origin:50% 0}.tippy-popper[x-placement^=top] .tippy-backdrop{-webkit-transform-origin:0 25%;transform-origin:0 25%}.tippy-popper[x-placement^=top] .tippy-backdrop[data-state=visible]{-webkit-transform:scale(1) translate(-50%,-55%);transform:scale(1) translate(-50%,-55%)}.tippy-popper[x-placement^=top] .tippy-backdrop[data-state=hidden]{-webkit-transform:scale(.2) translate(-50%,-45%);transform:scale(.2) translate(-50%,-45%);opacity:0}.tippy-popper[x-placement^=top] [data-animation=shift-toward][data-state=visible]{-webkit-transform:translateY(-10px);transform:translateY(-10px)}.tippy-popper[x-placement^=top] [data-animation=shift-toward][data-state=hidden]{opacity:0;-webkit-transform:translateY(-20px);transform:translateY(-20px)}.tippy-popper[x-placement^=top] [data-animation=perspective]{-webkit-transform-origin:bottom;transform-origin:bottom}.tippy-popper[x-placement^=top] [data-animation=perspective][data-state=visible]{-webkit-transform:perspective(700px) translateY(-10px);transform:perspective(700px) translateY(-10px)}.tippy-popper[x-placement^=top] [data-animation=perspective][data-state=hidden]{opacity:0;-webkit-transform:perspective(700px) rotateX(60deg);transform:perspective(700px) rotateX(60deg)}.tippy-popper[x-placement^=top] [data-animation=fade][data-state=visible]{-webkit-transform:translateY(-10px);transform:translateY(-10px)}.tippy-popper[x-placement^=top] [data-animation=fade][data-state=hidden]{opacity:0;-webkit-transform:translateY(-10px);transform:translateY(-10px)}.tippy-popper[x-placement^=top] [data-animation=shift-away][data-state=visible]{-webkit-transform:translateY(-10px);transform:translateY(-10px)}.tippy-popper[x-placement^=top] [data-animation=shift-away][data-state=hidden]{opacity:0}.tippy-popper[x-placement^=top] [data-animation=scale]{-webkit-transform-origin:bottom;transform-origin:bottom}.tippy-popper[x-placement^=top] [data-animation=scale][data-state=visible]{-webkit-transform:translateY(-10px);transform:translateY(-10px)}.tippy-popper[x-placement^=top] [data-animation=scale][data-state=hidden]{opacity:0;-webkit-transform:translateY(-10px) scale(.5);transform:translateY(-10px) scale(.5)}.tippy-popper[x-placement^=bottom] .tippy-backdrop{border-radius:0 0 30% 30%}.tippy-popper[x-placement^=bottom] .tippy-roundarrow{top:-7px;-webkit-transform-origin:50% 100%;transform-origin:50% 100%;margin:0 3px}.tippy-popper[x-placement^=bottom] .tippy-roundarrow svg{position:absolute;left:0}.tippy-popper[x-placement^=bottom] .tippy-arrow{border-bottom:8px solid #333;border-right:8px solid transparent;border-left:8px solid transparent;top:-7px;margin:0 3px;-webkit-transform-origin:50% 100%;transform-origin:50% 100%}.tippy-popper[x-placement^=bottom] .tippy-backdrop{-webkit-transform-origin:0 -50%;transform-origin:0 -50%}.tippy-popper[x-placement^=bottom] .tippy-backdrop[data-state=visible]{-webkit-transform:scale(1) translate(-50%,-45%);transform:scale(1) translate(-50%,-45%)}.tippy-popper[x-placement^=bottom] .tippy-backdrop[data-state=hidden]{-webkit-transform:scale(.2) translate(-50%);transform:scale(.2) translate(-50%);opacity:0}.tippy-popper[x-placement^=bottom] [data-animation=shift-toward][data-state=visible]{-webkit-transform:translateY(10px);transform:translateY(10px)}.tippy-popper[x-placement^=bottom] [data-animation=shift-toward][data-state=hidden]{opacity:0;-webkit-transform:translateY(20px);transform:translateY(20px)}.tippy-popper[x-placement^=bottom] [data-animation=perspective]{-webkit-transform-origin:top;transform-origin:top}.tippy-popper[x-placement^=bottom] [data-animation=perspective][data-state=visible]{-webkit-transform:perspective(700px) translateY(10px);transform:perspective(700px) translateY(10px)}.tippy-popper[x-placement^=bottom] [data-animation=perspective][data-state=hidden]{opacity:0;-webkit-transform:perspective(700px) rotateX(-60deg);transform:perspective(700px) rotateX(-60deg)}.tippy-popper[x-placement^=bottom] [data-animation=fade][data-state=visible]{-webkit-transform:translateY(10px);transform:translateY(10px)}.tippy-popper[x-placement^=bottom] [data-animation=fade][data-state=hidden]{opacity:0;-webkit-transform:translateY(10px);transform:translateY(10px)}.tippy-popper[x-placement^=bottom] [data-animation=shift-away][data-state=visible]{-webkit-transform:translateY(10px);transform:translateY(10px)}.tippy-popper[x-placement^=bottom] [data-animation=shift-away][data-state=hidden]{opacity:0}.tippy-popper[x-placement^=bottom] [data-animation=scale]{-webkit-transform-origin:top;transform-origin:top}.tippy-popper[x-placement^=bottom] [data-animation=scale][data-state=visible]{-webkit-transform:translateY(10px);transform:translateY(10px)}.tippy-popper[x-placement^=bottom] [data-animation=scale][data-state=hidden]{opacity:0;-webkit-transform:translateY(10px) scale(.5);transform:translateY(10px) scale(.5)}.tippy-popper[x-placement^=left] .tippy-backdrop{border-radius:50% 0 0 50%}.tippy-popper[x-placement^=left] .tippy-roundarrow{right:-12px;-webkit-transform-origin:33.33333333% 50%;transform-origin:33.33333333% 50%;margin:3px 0}.tippy-popper[x-placement^=left] .tippy-roundarrow svg{position:absolute;left:0;-webkit-transform:rotate(90deg);transform:rotate(90deg)}.tippy-popper[x-placement^=left] .tippy-arrow{border-left:8px solid #333;border-top:8px solid transparent;border-bottom:8px solid transparent;right:-7px;margin:3px 0;-webkit-transform-origin:0 50%;transform-origin:0 50%}.tippy-popper[x-placement^=left] .tippy-backdrop{-webkit-transform-origin:50% 0;transform-origin:50% 0}.tippy-popper[x-placement^=left] .tippy-backdrop[data-state=visible]{-webkit-transform:scale(1) translate(-50%,-50%);transform:scale(1) translate(-50%,-50%)}.tippy-popper[x-placement^=left] .tippy-backdrop[data-state=hidden]{-webkit-transform:scale(.2) translate(-75%,-50%);transform:scale(.2) translate(-75%,-50%);opacity:0}.tippy-popper[x-placement^=left] [data-animation=shift-toward][data-state=visible]{-webkit-transform:translateX(-10px);transform:translateX(-10px)}.tippy-popper[x-placement^=left] [data-animation=shift-toward][data-state=hidden]{opacity:0;-webkit-transform:translateX(-20px);transform:translateX(-20px)}.tippy-popper[x-placement^=left] [data-animation=perspective]{-webkit-transform-origin:right;transform-origin:right}.tippy-popper[x-placement^=left] [data-animation=perspective][data-state=visible]{-webkit-transform:perspective(700px) translateX(-10px);transform:perspective(700px) translateX(-10px)}.tippy-popper[x-placement^=left] [data-animation=perspective][data-state=hidden]{opacity:0;-webkit-transform:perspective(700px) rotateY(-60deg);transform:perspective(700px) rotateY(-60deg)}.tippy-popper[x-placement^=left] [data-animation=fade][data-state=visible]{-webkit-transform:translateX(-10px);transform:translateX(-10px)}.tippy-popper[x-placement^=left] [data-animation=fade][data-state=hidden]{opacity:0;-webkit-transform:translateX(-10px);transform:translateX(-10px)}.tippy-popper[x-placement^=left] [data-animation=shift-away][data-state=visible]{-webkit-transform:translateX(-10px);transform:translateX(-10px)}.tippy-popper[x-placement^=left] [data-animation=shift-away][data-state=hidden]{opacity:0}.tippy-popper[x-placement^=left] [data-animation=scale]{-webkit-transform-origin:right;transform-origin:right}.tippy-popper[x-placement^=left] [data-animation=scale][data-state=visible]{-webkit-transform:translateX(-10px);transform:translateX(-10px)}.tippy-popper[x-placement^=left] [data-animation=scale][data-state=hidden]{opacity:0;-webkit-transform:translateX(-10px) scale(.5);transform:translateX(-10px) scale(.5)}.tippy-popper[x-placement^=right] .tippy-backdrop{border-radius:0 50% 50% 0}.tippy-popper[x-placement^=right] .tippy-roundarrow{left:-12px;-webkit-transform-origin:66.66666666% 50%;transform-origin:66.66666666% 50%;margin:3px 0}.tippy-popper[x-placement^=right] .tippy-roundarrow svg{position:absolute;left:0;-webkit-transform:rotate(-90deg);transform:rotate(-90deg)}.tippy-popper[x-placement^=right] .tippy-arrow{border-right:8px solid #333;border-top:8px solid transparent;border-bottom:8px solid transparent;left:-7px;margin:3px 0;-webkit-transform-origin:100% 50%;transform-origin:100% 50%}.tippy-popper[x-placement^=right] .tippy-backdrop{-webkit-transform-origin:-50% 0;transform-origin:-50% 0}.tippy-popper[x-placement^=right] .tippy-backdrop[data-state=visible]{-webkit-transform:scale(1) translate(-50%,-50%);transform:scale(1) translate(-50%,-50%)}.tippy-popper[x-placement^=right] .tippy-backdrop[data-state=hidden]{-webkit-transform:scale(.2) translate(-25%,-50%);transform:scale(.2) translate(-25%,-50%);opacity:0}.tippy-popper[x-placement^=right] [data-animation=shift-toward][data-state=visible]{-webkit-transform:translateX(10px);transform:translateX(10px)}.tippy-popper[x-placement^=right] [data-animation=shift-toward][data-state=hidden]{opacity:0;-webkit-transform:translateX(20px);transform:translateX(20px)}.tippy-popper[x-placement^=right] [data-animation=perspective]{-webkit-transform-origin:left;transform-origin:left}.tippy-popper[x-placement^=right] [data-animation=perspective][data-state=visible]{-webkit-transform:perspective(700px) translateX(10px);transform:perspective(700px) translateX(10px)}.tippy-popper[x-placement^=right] [data-animation=perspective][data-state=hidden]{opacity:0;-webkit-transform:perspective(700px) rotateY(60deg);transform:perspective(700px) rotateY(60deg)}.tippy-popper[x-placement^=right] [data-animation=fade][data-state=visible]{-webkit-transform:translateX(10px);transform:translateX(10px)}.tippy-popper[x-placement^=right] [data-animation=fade][data-state=hidden]{opacity:0;-webkit-transform:translateX(10px);transform:translateX(10px)}.tippy-popper[x-placement^=right] [data-animation=shift-away][data-state=visible]{-webkit-transform:translateX(10px);transform:translateX(10px)}.tippy-popper[x-placement^=right] [data-animation=shift-away][data-state=hidden]{opacity:0}.tippy-popper[x-placement^=right] [data-animation=scale]{-webkit-transform-origin:left;transform-origin:left}.tippy-popper[x-placement^=right] [data-animation=scale][data-state=visible]{-webkit-transform:translateX(10px);transform:translateX(10px)}.tippy-popper[x-placement^=right] [data-animation=scale][data-state=hidden]{opacity:0;-webkit-transform:translateX(10px) scale(.5);transform:translateX(10px) scale(.5)}.tippy-tooltip{position:relative;color:#fff;border-radius:.25rem;font-size:.875rem;padding:.3125rem .5625rem;line-height:1.4;text-align:center;background-color:#333}.tippy-tooltip[data-size=small]{padding:.1875rem .375rem;font-size:.75rem}.tippy-tooltip[data-size=large]{padding:.375rem .75rem;font-size:1rem}.tippy-tooltip[data-animatefill]{overflow:hidden;background-color:initial}.tippy-tooltip[data-interactive],.tippy-tooltip[data-interactive] .tippy-roundarrow path{pointer-events:auto}.tippy-tooltip[data-inertia][data-state=visible]{transition-timing-function:cubic-bezier(.54,1.5,.38,1.11)}.tippy-tooltip[data-inertia][data-state=hidden]{transition-timing-function:ease}.tippy-arrow,.tippy-roundarrow{position:absolute;width:0;height:0}.tippy-roundarrow{width:18px;height:7px;fill:#333;pointer-events:none}.tippy-backdrop{position:absolute;background-color:#333;border-radius:50%;width:calc(110% + 2rem);left:50%;top:50%;z-index:-1;transition:all cubic-bezier(.46,.1,.52,.98);-webkit-backface-visibility:hidden;backface-visibility:hidden}.tippy-backdrop:after{content:"";float:left;padding-top:100%}.tippy-backdrop+.tippy-content{transition-property:opacity;will-change:opacity}.tippy-backdrop+.tippy-content[data-state=hidden]{opacity:0}'
        );
      var bt = ht,
        vt = n(10),
        gt = n.n(vt);
      var yt = (t, e, n = {}) => {
        document.addEventListener("DOMContentLoaded", () => {
          let r = document.querySelectorAll(t),
            o = {
              content: "Loading...",
              animateFill: !1,
              arrow: !0,
              interactive: !0,
              distance: 5,
              animation: "fade",
              flipOnUpdate: !0,
              theme: "wordlift"
            };
          r.forEach(t => {
            bt(t, {
              ...Object.assign(o, n),
              onShow: n => {
                void 0 === n.state.ajax &&
                  (n.state.ajax = { isFetching: !1, canFetch: !0 }),
                  !n.state.ajax.isFetching &&
                    n.state.ajax.canFetch &&
                    gt.a
                      .get(`${e}?entity_url=${t.getAttribute("href")}`)
                      .then(t => {
                        t.data &&
                          n.setContent(
                            (t => {
                              let e = t[0],
                                n = e.description;
                              if (!e.image) return n;
                              let r = e.image[0];
                              return r.width >= r.height
                                ? `<img src="${
                                    r.url
                                  }" style="width: 100%; height: 200px; object-fit: cover;" />${n}`
                                : `<img src="${
                                    r.url
                                  }" style="width: 50%; height: auto; float: right" />${n}<div style="clear: right"></div>`;
                            })(t.data)
                          );
                      })
                      .finally(() => {
                        n.state.ajax.isFetching = !1;
                      });
              },
              onHidden: t => {
                t.setContent("Loading..."), (t.state.ajax.canFetch = !0);
              }
            });
          });
        });
      };
      n.d(e, "contextCards", function() {
        return yt;
      });
    }
  ]);
});
