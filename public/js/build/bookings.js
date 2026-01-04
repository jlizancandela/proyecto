import {
  __commonJS,
  __esm,
  bootstrap_esm_exports,
  init_bootstrap_esm
} from "./chunk-JPOHOG3X.js";

// node_modules/preact/dist/preact.module.js
function d(n2, l3) {
  for (var u3 in l3) n2[u3] = l3[u3];
  return n2;
}
function g(n2) {
  n2 && n2.parentNode && n2.parentNode.removeChild(n2);
}
function _(l3, u3, t3) {
  var i3, r3, o3, e3 = {};
  for (o3 in u3) "key" == o3 ? i3 = u3[o3] : "ref" == o3 ? r3 = u3[o3] : e3[o3] = u3[o3];
  if (arguments.length > 2 && (e3.children = arguments.length > 3 ? n.call(arguments, 2) : t3), "function" == typeof l3 && null != l3.defaultProps) for (o3 in l3.defaultProps) void 0 === e3[o3] && (e3[o3] = l3.defaultProps[o3]);
  return m(l3, e3, i3, r3, null);
}
function m(n2, t3, i3, r3, o3) {
  var e3 = { type: n2, props: t3, key: i3, ref: r3, __k: null, __: null, __b: 0, __e: null, __c: null, constructor: void 0, __v: null == o3 ? ++u : o3, __i: -1, __u: 0 };
  return null == o3 && null != l.vnode && l.vnode(e3), e3;
}
function k(n2) {
  return n2.children;
}
function x(n2, l3) {
  this.props = n2, this.context = l3;
}
function S(n2, l3) {
  if (null == l3) return n2.__ ? S(n2.__, n2.__i + 1) : null;
  for (var u3; l3 < n2.__k.length; l3++) if (null != (u3 = n2.__k[l3]) && null != u3.__e) return u3.__e;
  return "function" == typeof n2.type ? S(n2) : null;
}
function C(n2) {
  var l3, u3;
  if (null != (n2 = n2.__) && null != n2.__c) {
    for (n2.__e = n2.__c.base = null, l3 = 0; l3 < n2.__k.length; l3++) if (null != (u3 = n2.__k[l3]) && null != u3.__e) {
      n2.__e = n2.__c.base = u3.__e;
      break;
    }
    return C(n2);
  }
}
function M(n2) {
  (!n2.__d && (n2.__d = true) && i.push(n2) && !$.__r++ || r != l.debounceRendering) && ((r = l.debounceRendering) || o)($);
}
function $() {
  for (var n2, u3, t3, r3, o3, f3, c3, s3 = 1; i.length; ) i.length > s3 && i.sort(e), n2 = i.shift(), s3 = i.length, n2.__d && (t3 = void 0, r3 = void 0, o3 = (r3 = (u3 = n2).__v).__e, f3 = [], c3 = [], u3.__P && ((t3 = d({}, r3)).__v = r3.__v + 1, l.vnode && l.vnode(t3), O(u3.__P, t3, r3, u3.__n, u3.__P.namespaceURI, 32 & r3.__u ? [o3] : null, f3, null == o3 ? S(r3) : o3, !!(32 & r3.__u), c3), t3.__v = r3.__v, t3.__.__k[t3.__i] = t3, N(f3, t3, c3), r3.__e = r3.__ = null, t3.__e != o3 && C(t3)));
  $.__r = 0;
}
function I(n2, l3, u3, t3, i3, r3, o3, e3, f3, c3, s3) {
  var a3, h3, y3, w3, d3, g2, _2, m3 = t3 && t3.__k || v, b = l3.length;
  for (f3 = P(u3, l3, m3, f3, b), a3 = 0; a3 < b; a3++) null != (y3 = u3.__k[a3]) && (h3 = -1 == y3.__i ? p : m3[y3.__i] || p, y3.__i = a3, g2 = O(n2, y3, h3, i3, r3, o3, e3, f3, c3, s3), w3 = y3.__e, y3.ref && h3.ref != y3.ref && (h3.ref && B(h3.ref, null, y3), s3.push(y3.ref, y3.__c || w3, y3)), null == d3 && null != w3 && (d3 = w3), (_2 = !!(4 & y3.__u)) || h3.__k === y3.__k ? f3 = A(y3, f3, n2, _2) : "function" == typeof y3.type && void 0 !== g2 ? f3 = g2 : w3 && (f3 = w3.nextSibling), y3.__u &= -7);
  return u3.__e = d3, f3;
}
function P(n2, l3, u3, t3, i3) {
  var r3, o3, e3, f3, c3, s3 = u3.length, a3 = s3, h3 = 0;
  for (n2.__k = new Array(i3), r3 = 0; r3 < i3; r3++) null != (o3 = l3[r3]) && "boolean" != typeof o3 && "function" != typeof o3 ? ("string" == typeof o3 || "number" == typeof o3 || "bigint" == typeof o3 || o3.constructor == String ? o3 = n2.__k[r3] = m(null, o3, null, null, null) : w(o3) ? o3 = n2.__k[r3] = m(k, { children: o3 }, null, null, null) : null == o3.constructor && o3.__b > 0 ? o3 = n2.__k[r3] = m(o3.type, o3.props, o3.key, o3.ref ? o3.ref : null, o3.__v) : n2.__k[r3] = o3, f3 = r3 + h3, o3.__ = n2, o3.__b = n2.__b + 1, e3 = null, -1 != (c3 = o3.__i = L(o3, u3, f3, a3)) && (a3--, (e3 = u3[c3]) && (e3.__u |= 2)), null == e3 || null == e3.__v ? (-1 == c3 && (i3 > s3 ? h3-- : i3 < s3 && h3++), "function" != typeof o3.type && (o3.__u |= 4)) : c3 != f3 && (c3 == f3 - 1 ? h3-- : c3 == f3 + 1 ? h3++ : (c3 > f3 ? h3-- : h3++, o3.__u |= 4))) : n2.__k[r3] = null;
  if (a3) for (r3 = 0; r3 < s3; r3++) null != (e3 = u3[r3]) && 0 == (2 & e3.__u) && (e3.__e == t3 && (t3 = S(e3)), D(e3, e3));
  return t3;
}
function A(n2, l3, u3, t3) {
  var i3, r3;
  if ("function" == typeof n2.type) {
    for (i3 = n2.__k, r3 = 0; i3 && r3 < i3.length; r3++) i3[r3] && (i3[r3].__ = n2, l3 = A(i3[r3], l3, u3, t3));
    return l3;
  }
  n2.__e != l3 && (t3 && (l3 && n2.type && !l3.parentNode && (l3 = S(n2)), u3.insertBefore(n2.__e, l3 || null)), l3 = n2.__e);
  do {
    l3 = l3 && l3.nextSibling;
  } while (null != l3 && 8 == l3.nodeType);
  return l3;
}
function L(n2, l3, u3, t3) {
  var i3, r3, o3, e3 = n2.key, f3 = n2.type, c3 = l3[u3], s3 = null != c3 && 0 == (2 & c3.__u);
  if (null === c3 && null == e3 || s3 && e3 == c3.key && f3 == c3.type) return u3;
  if (t3 > (s3 ? 1 : 0)) {
    for (i3 = u3 - 1, r3 = u3 + 1; i3 >= 0 || r3 < l3.length; ) if (null != (c3 = l3[o3 = i3 >= 0 ? i3-- : r3++]) && 0 == (2 & c3.__u) && e3 == c3.key && f3 == c3.type) return o3;
  }
  return -1;
}
function T(n2, l3, u3) {
  "-" == l3[0] ? n2.setProperty(l3, null == u3 ? "" : u3) : n2[l3] = null == u3 ? "" : "number" != typeof u3 || y.test(l3) ? u3 : u3 + "px";
}
function j(n2, l3, u3, t3, i3) {
  var r3, o3;
  n: if ("style" == l3) if ("string" == typeof u3) n2.style.cssText = u3;
  else {
    if ("string" == typeof t3 && (n2.style.cssText = t3 = ""), t3) for (l3 in t3) u3 && l3 in u3 || T(n2.style, l3, "");
    if (u3) for (l3 in u3) t3 && u3[l3] == t3[l3] || T(n2.style, l3, u3[l3]);
  }
  else if ("o" == l3[0] && "n" == l3[1]) r3 = l3 != (l3 = l3.replace(f, "$1")), o3 = l3.toLowerCase(), l3 = o3 in n2 || "onFocusOut" == l3 || "onFocusIn" == l3 ? o3.slice(2) : l3.slice(2), n2.l || (n2.l = {}), n2.l[l3 + r3] = u3, u3 ? t3 ? u3.u = t3.u : (u3.u = c, n2.addEventListener(l3, r3 ? a : s, r3)) : n2.removeEventListener(l3, r3 ? a : s, r3);
  else {
    if ("http://www.w3.org/2000/svg" == i3) l3 = l3.replace(/xlink(H|:h)/, "h").replace(/sName$/, "s");
    else if ("width" != l3 && "height" != l3 && "href" != l3 && "list" != l3 && "form" != l3 && "tabIndex" != l3 && "download" != l3 && "rowSpan" != l3 && "colSpan" != l3 && "role" != l3 && "popover" != l3 && l3 in n2) try {
      n2[l3] = null == u3 ? "" : u3;
      break n;
    } catch (n3) {
    }
    "function" == typeof u3 || (null == u3 || false === u3 && "-" != l3[4] ? n2.removeAttribute(l3) : n2.setAttribute(l3, "popover" == l3 && 1 == u3 ? "" : u3));
  }
}
function F(n2) {
  return function(u3) {
    if (this.l) {
      var t3 = this.l[u3.type + n2];
      if (null == u3.t) u3.t = c++;
      else if (u3.t < t3.u) return;
      return t3(l.event ? l.event(u3) : u3);
    }
  };
}
function O(n2, u3, t3, i3, r3, o3, e3, f3, c3, s3) {
  var a3, h3, p3, v3, y3, _2, m3, b, S2, C3, M2, $2, P2, A2, H, L2, T2, j3 = u3.type;
  if (null != u3.constructor) return null;
  128 & t3.__u && (c3 = !!(32 & t3.__u), o3 = [f3 = u3.__e = t3.__e]), (a3 = l.__b) && a3(u3);
  n: if ("function" == typeof j3) try {
    if (b = u3.props, S2 = "prototype" in j3 && j3.prototype.render, C3 = (a3 = j3.contextType) && i3[a3.__c], M2 = a3 ? C3 ? C3.props.value : a3.__ : i3, t3.__c ? m3 = (h3 = u3.__c = t3.__c).__ = h3.__E : (S2 ? u3.__c = h3 = new j3(b, M2) : (u3.__c = h3 = new x(b, M2), h3.constructor = j3, h3.render = E), C3 && C3.sub(h3), h3.state || (h3.state = {}), h3.__n = i3, p3 = h3.__d = true, h3.__h = [], h3._sb = []), S2 && null == h3.__s && (h3.__s = h3.state), S2 && null != j3.getDerivedStateFromProps && (h3.__s == h3.state && (h3.__s = d({}, h3.__s)), d(h3.__s, j3.getDerivedStateFromProps(b, h3.__s))), v3 = h3.props, y3 = h3.state, h3.__v = u3, p3) S2 && null == j3.getDerivedStateFromProps && null != h3.componentWillMount && h3.componentWillMount(), S2 && null != h3.componentDidMount && h3.__h.push(h3.componentDidMount);
    else {
      if (S2 && null == j3.getDerivedStateFromProps && b !== v3 && null != h3.componentWillReceiveProps && h3.componentWillReceiveProps(b, M2), u3.__v == t3.__v || !h3.__e && null != h3.shouldComponentUpdate && false === h3.shouldComponentUpdate(b, h3.__s, M2)) {
        for (u3.__v != t3.__v && (h3.props = b, h3.state = h3.__s, h3.__d = false), u3.__e = t3.__e, u3.__k = t3.__k, u3.__k.some(function(n3) {
          n3 && (n3.__ = u3);
        }), $2 = 0; $2 < h3._sb.length; $2++) h3.__h.push(h3._sb[$2]);
        h3._sb = [], h3.__h.length && e3.push(h3);
        break n;
      }
      null != h3.componentWillUpdate && h3.componentWillUpdate(b, h3.__s, M2), S2 && null != h3.componentDidUpdate && h3.__h.push(function() {
        h3.componentDidUpdate(v3, y3, _2);
      });
    }
    if (h3.context = M2, h3.props = b, h3.__P = n2, h3.__e = false, P2 = l.__r, A2 = 0, S2) {
      for (h3.state = h3.__s, h3.__d = false, P2 && P2(u3), a3 = h3.render(h3.props, h3.state, h3.context), H = 0; H < h3._sb.length; H++) h3.__h.push(h3._sb[H]);
      h3._sb = [];
    } else do {
      h3.__d = false, P2 && P2(u3), a3 = h3.render(h3.props, h3.state, h3.context), h3.state = h3.__s;
    } while (h3.__d && ++A2 < 25);
    h3.state = h3.__s, null != h3.getChildContext && (i3 = d(d({}, i3), h3.getChildContext())), S2 && !p3 && null != h3.getSnapshotBeforeUpdate && (_2 = h3.getSnapshotBeforeUpdate(v3, y3)), L2 = a3, null != a3 && a3.type === k && null == a3.key && (L2 = V(a3.props.children)), f3 = I(n2, w(L2) ? L2 : [L2], u3, t3, i3, r3, o3, e3, f3, c3, s3), h3.base = u3.__e, u3.__u &= -161, h3.__h.length && e3.push(h3), m3 && (h3.__E = h3.__ = null);
  } catch (n3) {
    if (u3.__v = null, c3 || null != o3) if (n3.then) {
      for (u3.__u |= c3 ? 160 : 128; f3 && 8 == f3.nodeType && f3.nextSibling; ) f3 = f3.nextSibling;
      o3[o3.indexOf(f3)] = null, u3.__e = f3;
    } else {
      for (T2 = o3.length; T2--; ) g(o3[T2]);
      z(u3);
    }
    else u3.__e = t3.__e, u3.__k = t3.__k, n3.then || z(u3);
    l.__e(n3, u3, t3);
  }
  else null == o3 && u3.__v == t3.__v ? (u3.__k = t3.__k, u3.__e = t3.__e) : f3 = u3.__e = q(t3.__e, u3, t3, i3, r3, o3, e3, c3, s3);
  return (a3 = l.diffed) && a3(u3), 128 & u3.__u ? void 0 : f3;
}
function z(n2) {
  n2 && n2.__c && (n2.__c.__e = true), n2 && n2.__k && n2.__k.forEach(z);
}
function N(n2, u3, t3) {
  for (var i3 = 0; i3 < t3.length; i3++) B(t3[i3], t3[++i3], t3[++i3]);
  l.__c && l.__c(u3, n2), n2.some(function(u4) {
    try {
      n2 = u4.__h, u4.__h = [], n2.some(function(n3) {
        n3.call(u4);
      });
    } catch (n3) {
      l.__e(n3, u4.__v);
    }
  });
}
function V(n2) {
  return "object" != typeof n2 || null == n2 || n2.__b && n2.__b > 0 ? n2 : w(n2) ? n2.map(V) : d({}, n2);
}
function q(u3, t3, i3, r3, o3, e3, f3, c3, s3) {
  var a3, h3, v3, y3, d3, _2, m3, b = i3.props || p, k3 = t3.props, x2 = t3.type;
  if ("svg" == x2 ? o3 = "http://www.w3.org/2000/svg" : "math" == x2 ? o3 = "http://www.w3.org/1998/Math/MathML" : o3 || (o3 = "http://www.w3.org/1999/xhtml"), null != e3) {
    for (a3 = 0; a3 < e3.length; a3++) if ((d3 = e3[a3]) && "setAttribute" in d3 == !!x2 && (x2 ? d3.localName == x2 : 3 == d3.nodeType)) {
      u3 = d3, e3[a3] = null;
      break;
    }
  }
  if (null == u3) {
    if (null == x2) return document.createTextNode(k3);
    u3 = document.createElementNS(o3, x2, k3.is && k3), c3 && (l.__m && l.__m(t3, e3), c3 = false), e3 = null;
  }
  if (null == x2) b === k3 || c3 && u3.data == k3 || (u3.data = k3);
  else {
    if (e3 = e3 && n.call(u3.childNodes), !c3 && null != e3) for (b = {}, a3 = 0; a3 < u3.attributes.length; a3++) b[(d3 = u3.attributes[a3]).name] = d3.value;
    for (a3 in b) if (d3 = b[a3], "children" == a3) ;
    else if ("dangerouslySetInnerHTML" == a3) v3 = d3;
    else if (!(a3 in k3)) {
      if ("value" == a3 && "defaultValue" in k3 || "checked" == a3 && "defaultChecked" in k3) continue;
      j(u3, a3, null, d3, o3);
    }
    for (a3 in k3) d3 = k3[a3], "children" == a3 ? y3 = d3 : "dangerouslySetInnerHTML" == a3 ? h3 = d3 : "value" == a3 ? _2 = d3 : "checked" == a3 ? m3 = d3 : c3 && "function" != typeof d3 || b[a3] === d3 || j(u3, a3, d3, b[a3], o3);
    if (h3) c3 || v3 && (h3.__html == v3.__html || h3.__html == u3.innerHTML) || (u3.innerHTML = h3.__html), t3.__k = [];
    else if (v3 && (u3.innerHTML = ""), I("template" == t3.type ? u3.content : u3, w(y3) ? y3 : [y3], t3, i3, r3, "foreignObject" == x2 ? "http://www.w3.org/1999/xhtml" : o3, e3, f3, e3 ? e3[0] : i3.__k && S(i3, 0), c3, s3), null != e3) for (a3 = e3.length; a3--; ) g(e3[a3]);
    c3 || (a3 = "value", "progress" == x2 && null == _2 ? u3.removeAttribute("value") : null != _2 && (_2 !== u3[a3] || "progress" == x2 && !_2 || "option" == x2 && _2 != b[a3]) && j(u3, a3, _2, b[a3], o3), a3 = "checked", null != m3 && m3 != u3[a3] && j(u3, a3, m3, b[a3], o3));
  }
  return u3;
}
function B(n2, u3, t3) {
  try {
    if ("function" == typeof n2) {
      var i3 = "function" == typeof n2.__u;
      i3 && n2.__u(), i3 && null == u3 || (n2.__u = n2(u3));
    } else n2.current = u3;
  } catch (n3) {
    l.__e(n3, t3);
  }
}
function D(n2, u3, t3) {
  var i3, r3;
  if (l.unmount && l.unmount(n2), (i3 = n2.ref) && (i3.current && i3.current != n2.__e || B(i3, null, u3)), null != (i3 = n2.__c)) {
    if (i3.componentWillUnmount) try {
      i3.componentWillUnmount();
    } catch (n3) {
      l.__e(n3, u3);
    }
    i3.base = i3.__P = null;
  }
  if (i3 = n2.__k) for (r3 = 0; r3 < i3.length; r3++) i3[r3] && D(i3[r3], u3, t3 || "function" != typeof n2.type);
  t3 || g(n2.__e), n2.__c = n2.__ = n2.__e = void 0;
}
function E(n2, l3, u3) {
  return this.constructor(n2, u3);
}
function G(u3, t3, i3) {
  var r3, o3, e3, f3;
  t3 == document && (t3 = document.documentElement), l.__ && l.__(u3, t3), o3 = (r3 = "function" == typeof i3) ? null : i3 && i3.__k || t3.__k, e3 = [], f3 = [], O(t3, u3 = (!r3 && i3 || t3).__k = _(k, null, [u3]), o3 || p, p, t3.namespaceURI, !r3 && i3 ? [i3] : o3 ? null : t3.firstChild ? n.call(t3.childNodes) : null, e3, !r3 && i3 ? i3 : o3 ? o3.__e : t3.firstChild, r3, f3), N(e3, u3, f3);
}
var n, l, u, t, i, r, o, e, f, c, s, a, h, p, v, y, w;
var init_preact_module = __esm({
  "node_modules/preact/dist/preact.module.js"() {
    p = {};
    v = [];
    y = /acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i;
    w = Array.isArray;
    n = v.slice, l = { __e: function(n2, l3, u3, t3) {
      for (var i3, r3, o3; l3 = l3.__; ) if ((i3 = l3.__c) && !i3.__) try {
        if ((r3 = i3.constructor) && null != r3.getDerivedStateFromError && (i3.setState(r3.getDerivedStateFromError(n2)), o3 = i3.__d), null != i3.componentDidCatch && (i3.componentDidCatch(n2, t3 || {}), o3 = i3.__d), o3) return i3.__E = i3;
      } catch (l4) {
        n2 = l4;
      }
      throw n2;
    } }, u = 0, t = function(n2) {
      return null != n2 && null == n2.constructor;
    }, x.prototype.setState = function(n2, l3) {
      var u3;
      u3 = null != this.__s && this.__s != this.state ? this.__s : this.__s = d({}, this.state), "function" == typeof n2 && (n2 = n2(d({}, u3), this.props)), n2 && d(u3, n2), null != n2 && this.__v && (l3 && this._sb.push(l3), M(this));
    }, x.prototype.forceUpdate = function(n2) {
      this.__v && (this.__e = true, n2 && this.__h.push(n2), M(this));
    }, x.prototype.render = k, i = [], o = "function" == typeof Promise ? Promise.prototype.then.bind(Promise.resolve()) : setTimeout, e = function(n2, l3) {
      return n2.__v.__b - l3.__v.__b;
    }, $.__r = 0, f = /(PointerCapture)$|Capture$/i, c = 0, s = F(false), a = F(true), h = 0;
  }
});

// node_modules/preact/hooks/dist/hooks.module.js
function p2(n2, t3) {
  c2.__h && c2.__h(r2, n2, o2 || t3), o2 = 0;
  var u3 = r2.__H || (r2.__H = { __: [], __h: [] });
  return n2 >= u3.__.length && u3.__.push({}), u3.__[n2];
}
function d2(n2) {
  return o2 = 1, h2(D2, n2);
}
function h2(n2, u3, i3) {
  var o3 = p2(t2++, 2);
  if (o3.t = n2, !o3.__c && (o3.__ = [i3 ? i3(u3) : D2(void 0, u3), function(n3) {
    var t3 = o3.__N ? o3.__N[0] : o3.__[0], r3 = o3.t(t3, n3);
    t3 !== r3 && (o3.__N = [r3, o3.__[1]], o3.__c.setState({}));
  }], o3.__c = r2, !r2.__f)) {
    var f3 = function(n3, t3, r3) {
      if (!o3.__c.__H) return true;
      var u4 = o3.__c.__H.__.filter(function(n4) {
        return !!n4.__c;
      });
      if (u4.every(function(n4) {
        return !n4.__N;
      })) return !c3 || c3.call(this, n3, t3, r3);
      var i4 = o3.__c.props !== n3;
      return u4.forEach(function(n4) {
        if (n4.__N) {
          var t4 = n4.__[0];
          n4.__ = n4.__N, n4.__N = void 0, t4 !== n4.__[0] && (i4 = true);
        }
      }), c3 && c3.call(this, n3, t3, r3) || i4;
    };
    r2.__f = true;
    var c3 = r2.shouldComponentUpdate, e3 = r2.componentWillUpdate;
    r2.componentWillUpdate = function(n3, t3, r3) {
      if (this.__e) {
        var u4 = c3;
        c3 = void 0, f3(n3, t3, r3), c3 = u4;
      }
      e3 && e3.call(this, n3, t3, r3);
    }, r2.shouldComponentUpdate = f3;
  }
  return o3.__N || o3.__;
}
function y2(n2, u3) {
  var i3 = p2(t2++, 3);
  !c2.__s && C2(i3.__H, u3) && (i3.__ = n2, i3.u = u3, r2.__H.__h.push(i3));
}
function j2() {
  for (var n2; n2 = f2.shift(); ) if (n2.__P && n2.__H) try {
    n2.__H.__h.forEach(z2), n2.__H.__h.forEach(B2), n2.__H.__h = [];
  } catch (t3) {
    n2.__H.__h = [], c2.__e(t3, n2.__v);
  }
}
function w2(n2) {
  var t3, r3 = function() {
    clearTimeout(u3), k2 && cancelAnimationFrame(t3), setTimeout(n2);
  }, u3 = setTimeout(r3, 35);
  k2 && (t3 = requestAnimationFrame(r3));
}
function z2(n2) {
  var t3 = r2, u3 = n2.__c;
  "function" == typeof u3 && (n2.__c = void 0, u3()), r2 = t3;
}
function B2(n2) {
  var t3 = r2;
  n2.__c = n2.__(), r2 = t3;
}
function C2(n2, t3) {
  return !n2 || n2.length !== t3.length || t3.some(function(t4, r3) {
    return t4 !== n2[r3];
  });
}
function D2(n2, t3) {
  return "function" == typeof t3 ? t3(n2) : t3;
}
var t2, r2, u2, i2, o2, f2, c2, e2, a2, v2, l2, m2, s2, k2;
var init_hooks_module = __esm({
  "node_modules/preact/hooks/dist/hooks.module.js"() {
    init_preact_module();
    o2 = 0;
    f2 = [];
    c2 = l;
    e2 = c2.__b;
    a2 = c2.__r;
    v2 = c2.diffed;
    l2 = c2.__c;
    m2 = c2.unmount;
    s2 = c2.__;
    c2.__b = function(n2) {
      r2 = null, e2 && e2(n2);
    }, c2.__ = function(n2, t3) {
      n2 && t3.__k && t3.__k.__m && (n2.__m = t3.__k.__m), s2 && s2(n2, t3);
    }, c2.__r = function(n2) {
      a2 && a2(n2), t2 = 0;
      var i3 = (r2 = n2.__c).__H;
      i3 && (u2 === r2 ? (i3.__h = [], r2.__h = [], i3.__.forEach(function(n3) {
        n3.__N && (n3.__ = n3.__N), n3.u = n3.__N = void 0;
      })) : (i3.__h.forEach(z2), i3.__h.forEach(B2), i3.__h = [], t2 = 0)), u2 = r2;
    }, c2.diffed = function(n2) {
      v2 && v2(n2);
      var t3 = n2.__c;
      t3 && t3.__H && (t3.__H.__h.length && (1 !== f2.push(t3) && i2 === c2.requestAnimationFrame || ((i2 = c2.requestAnimationFrame) || w2)(j2)), t3.__H.__.forEach(function(n3) {
        n3.u && (n3.__H = n3.u), n3.u = void 0;
      })), u2 = r2 = null;
    }, c2.__c = function(n2, t3) {
      t3.some(function(n3) {
        try {
          n3.__h.forEach(z2), n3.__h = n3.__h.filter(function(n4) {
            return !n4.__ || B2(n4);
          });
        } catch (r3) {
          t3.some(function(n4) {
            n4.__h && (n4.__h = []);
          }), t3 = [], c2.__e(r3, n3.__v);
        }
      }), l2 && l2(n2, t3);
    }, c2.unmount = function(n2) {
      m2 && m2(n2);
      var t3, r3 = n2.__c;
      r3 && r3.__H && (r3.__H.__.forEach(function(n3) {
        try {
          z2(n3);
        } catch (n4) {
          t3 = n4;
        }
      }), r3.__H = void 0, t3 && c2.__e(t3, r3.__v));
    };
    k2 = "function" == typeof requestAnimationFrame;
  }
});

// node_modules/nanostores/clean-stores/index.js
var clean;
var init_clean_stores = __esm({
  "node_modules/nanostores/clean-stores/index.js"() {
    clean = /* @__PURE__ */ Symbol("clean");
  }
});

// node_modules/nanostores/atom/index.js
var listenerQueue, lqIndex, QUEUE_ITEMS_PER_LISTENER, epoch, atom;
var init_atom = __esm({
  "node_modules/nanostores/atom/index.js"() {
    init_clean_stores();
    listenerQueue = [];
    lqIndex = 0;
    QUEUE_ITEMS_PER_LISTENER = 4;
    epoch = 0;
    atom = /* @__NO_SIDE_EFFECTS__ */ (initialValue) => {
      let listeners = [];
      let $atom = {
        get() {
          if (!$atom.lc) {
            $atom.listen(() => {
            })();
          }
          return $atom.value;
        },
        lc: 0,
        listen(listener) {
          $atom.lc = listeners.push(listener);
          return () => {
            for (let i3 = lqIndex + QUEUE_ITEMS_PER_LISTENER; i3 < listenerQueue.length; ) {
              if (listenerQueue[i3] === listener) {
                listenerQueue.splice(i3, QUEUE_ITEMS_PER_LISTENER);
              } else {
                i3 += QUEUE_ITEMS_PER_LISTENER;
              }
            }
            let index = listeners.indexOf(listener);
            if (~index) {
              listeners.splice(index, 1);
              if (!--$atom.lc) $atom.off();
            }
          };
        },
        notify(oldValue, changedKey) {
          epoch++;
          let runListenerQueue = !listenerQueue.length;
          for (let listener of listeners) {
            listenerQueue.push(listener, $atom.value, oldValue, changedKey);
          }
          if (runListenerQueue) {
            for (lqIndex = 0; lqIndex < listenerQueue.length; lqIndex += QUEUE_ITEMS_PER_LISTENER) {
              listenerQueue[lqIndex](
                listenerQueue[lqIndex + 1],
                listenerQueue[lqIndex + 2],
                listenerQueue[lqIndex + 3]
              );
            }
            listenerQueue.length = 0;
          }
        },
        /* It will be called on last listener unsubscribing.
           We will redefine it in onMount and onStop. */
        off() {
        },
        set(newValue) {
          let oldValue = $atom.value;
          if (oldValue !== newValue) {
            $atom.value = newValue;
            $atom.notify(oldValue);
          }
        },
        subscribe(listener) {
          let unbind = $atom.listen(listener);
          listener($atom.value);
          return unbind;
        },
        value: initialValue
      };
      if (true) {
        $atom[clean] = () => {
          listeners = [];
          $atom.lc = 0;
          $atom.off();
        };
      }
      return $atom;
    };
  }
});

// node_modules/nanostores/listen-keys/index.js
function listenKeys($store, keys, listener) {
  let keysSet = new Set(keys).add(void 0);
  return $store.listen((value, oldValue, changed) => {
    if (keysSet.has(changed)) {
      listener(value, oldValue, changed);
    }
  });
}
var init_listen_keys = __esm({
  "node_modules/nanostores/listen-keys/index.js"() {
  }
});

// node_modules/nanostores/map/index.js
var map;
var init_map = __esm({
  "node_modules/nanostores/map/index.js"() {
    init_atom();
    map = /* @__NO_SIDE_EFFECTS__ */ (initial = {}) => {
      let $map = atom(initial);
      $map.setKey = function(key, value) {
        let oldMap = $map.value;
        if (typeof value === "undefined" && key in $map.value) {
          $map.value = { ...$map.value };
          delete $map.value[key];
          $map.notify(oldMap, key);
        } else if ($map.value[key] !== value) {
          $map.value = {
            ...$map.value,
            [key]: value
          };
          $map.notify(oldMap, key);
        }
      };
      return $map;
    };
  }
});

// node_modules/nanostores/index.js
var init_nanostores = __esm({
  "node_modules/nanostores/index.js"() {
    init_atom();
    init_listen_keys();
    init_map();
  }
});

// node_modules/@nanostores/preact/index.js
function useStore(store, opts = {}) {
  let [, forceRender] = d2({});
  let [valueBeforeEffect] = d2(store.get());
  y2(() => {
    valueBeforeEffect !== store.get() && forceRender({});
  }, []);
  y2(() => {
    let batching, timer, unlisten;
    let rerender = () => {
      if (!batching) {
        batching = 1;
        timer = setTimeout(() => {
          batching = void 0;
          forceRender({});
        });
      }
    };
    if (opts.keys) {
      unlisten = listenKeys(store, opts.keys, rerender);
    } else {
      unlisten = store.listen(rerender);
    }
    return () => {
      unlisten();
      clearTimeout(timer);
    };
  }, [store, "" + opts.keys]);
  return store.get();
}
var init_preact = __esm({
  "node_modules/@nanostores/preact/index.js"() {
    init_nanostores();
    init_hooks_module();
  }
});

// src/js/user/bookings/api/bookingsApi.js
var SERVICES_API_URL, AVAILABLE_SPECIALISTS_API_URL, USER_BOOKINGS_API_URL, CURRENT_USER_API_URL, getServices, getEspecialistasDisponibles, getUserBookings, createReserva, getCurrentUser;
var init_bookingsApi = __esm({
  "src/js/user/bookings/api/bookingsApi.js"() {
    SERVICES_API_URL = "/api/services";
    AVAILABLE_SPECIALISTS_API_URL = "/api/especialistas/disponibles";
    USER_BOOKINGS_API_URL = "/api/reservas";
    CURRENT_USER_API_URL = "/api/me";
    getServices = async () => {
      try {
        const response = await fetch(SERVICES_API_URL);
        if (!response.ok) {
          throw new Error("Error al obtener servicios");
        }
        const data = await response.json();
        return data;
      } catch (error) {
        console.error("Error al cargar servicios:", error);
        return [];
      }
    };
    getEspecialistasDisponibles = async (idServicio, fecha, limit = null, offset = null) => {
      if (!idServicio) {
        return { data: [], total: 0 };
      }
      if (!fecha) {
        return { data: [], total: 0 };
      }
      try {
        let url = `${AVAILABLE_SPECIALISTS_API_URL}?servicio=${idServicio}&fecha=${fecha}`;
        if (limit !== null) {
          url += `&limit=${limit}`;
        }
        if (offset !== null) {
          url += `&offset=${offset}`;
        }
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error("Error al obtener especialistas disponibles");
        }
        const data = await response.json();
        return data;
      } catch (error) {
        console.error("Error al cargar especialistas:", error);
        return { data: [], total: 0 };
      }
    };
    getUserBookings = async () => {
      try {
        const response = await fetch(USER_BOOKINGS_API_URL);
        if (!response.ok) {
          throw new Error("Error al obtener reservas");
        }
        const data = await response.json();
        return data.reservas || [];
      } catch (error) {
        console.error("Error al cargar reservas:", error);
        return [];
      }
    };
    createReserva = async (reservaData) => {
      if (!reservaData || typeof reservaData !== "object") {
        throw new Error("Los datos de la reserva son obligatorios");
      }
      const { servicio_id, especialista_id, fecha, hora } = reservaData;
      if (!servicio_id) {
        throw new Error("Debes seleccionar un servicio");
      }
      if (!especialista_id) {
        throw new Error("Debes seleccionar un especialista");
      }
      if (!fecha) {
        throw new Error("Debes seleccionar una fecha");
      }
      if (!hora) {
        throw new Error("Debes seleccionar una hora");
      }
      try {
        const response = await fetch(USER_BOOKINGS_API_URL, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(reservaData)
        });
        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.error || "Error al crear la reserva");
        }
        return await response.json();
      } catch (error) {
        console.error("Error al crear reserva:", error);
        throw error;
      }
    };
    getCurrentUser = async () => {
      try {
        const response = await fetch(CURRENT_USER_API_URL);
        if (!response.ok) return null;
        const data = await response.json();
        return data.success ? data.data : null;
      } catch (error) {
        console.error("Error al obtener usuario:", error);
        return null;
      }
    };
  }
});

// src/js/user/bookings/tools/formatters.js
var formatearFechaLarga, formatearFechaISO, esHoy, isPastTime;
var init_formatters = __esm({
  "src/js/user/bookings/tools/formatters.js"() {
    formatearFechaLarga = (fecha, capitalizar = true) => {
      const fechaFormateada = fecha.toLocaleDateString("es-ES", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric"
      });
      return capitalizar ? fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1) : fechaFormateada;
    };
    formatearFechaISO = (fecha) => {
      const year = fecha.getFullYear();
      const month = String(fecha.getMonth() + 1).padStart(2, "0");
      const day = String(fecha.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    };
    esHoy = (fecha) => {
      const hoy = /* @__PURE__ */ new Date();
      return fecha.getDate() === hoy.getDate() && fecha.getMonth() === hoy.getMonth() && fecha.getFullYear() === hoy.getFullYear();
    };
    isPastTime = (fecha, hora) => {
      if (!esHoy(fecha)) {
        return false;
      }
      const [horas, minutos] = hora.split(":").map(Number);
      const horaSeleccionada = /* @__PURE__ */ new Date();
      horaSeleccionada.setHours(horas, minutos, 0, 0);
      const ahora = /* @__PURE__ */ new Date();
      return horaSeleccionada <= ahora;
    };
  }
});

// src/js/user/bookings/tools/validators.js
var getWeekStart, isDateInSameWeek, hasWeeklyBookingForService, getTotalWeeklyHours, exceedsWeeklyHoursLimit;
var init_validators = __esm({
  "src/js/user/bookings/tools/validators.js"() {
    getWeekStart = (date) => {
      const d3 = new Date(date);
      const day = d3.getDay();
      const diff = d3.getDate() - day + (day === 0 ? -6 : 1);
      return new Date(d3.setDate(diff));
    };
    isDateInSameWeek = (date1, date2) => {
      const week1Start = getWeekStart(date1);
      const week2Start = getWeekStart(date2);
      return week1Start.getTime() === week2Start.getTime();
    };
    hasWeeklyBookingForService = (bookings, serviceId, targetDate) => {
      const activeBookings = bookings.filter((booking) => booking.estado !== "Cancelada");
      return activeBookings.some((booking) => {
        const isSameService = booking.id_servicio === serviceId;
        const isSameWeek = isDateInSameWeek(new Date(booking.fecha_reserva), new Date(targetDate));
        return isSameService && isSameWeek;
      });
    };
    getTotalWeeklyHours = (bookings, targetDate) => {
      const activeBookings = bookings.filter((booking) => booking.estado !== "Cancelada");
      const target = new Date(targetDate);
      const totalMinutes = activeBookings.reduce((sum, booking) => {
        if (isDateInSameWeek(new Date(booking.fecha_reserva), target)) {
          return sum + (booking.duracion_minutos || 60);
        }
        return sum;
      }, 0);
      return totalMinutes / 60;
    };
    exceedsWeeklyHoursLimit = (bookings, targetDate, newDuration, limit = 40) => {
      const currentHours = getTotalWeeklyHours(bookings, targetDate);
      return currentHours + newDuration / 60 > limit;
    };
  }
});

// src/js/user/bookings/context/bookingsStore.js
var $estado, $services, $especialistas, $userName, $bookingDraft, $pagination, $uiState, $mes, $totalEspecialistas, loadUserAction, loadServicesAction, selectServiceAction, setMesAction, setDiaAction, loadEspecialistasAction, selectEspecialistaAction, changePageAction, confirmReservaAction, resetBookingAction;
var init_bookingsStore = __esm({
  "src/js/user/bookings/context/bookingsStore.js"() {
    init_nanostores();
    init_bookingsApi();
    init_formatters();
    init_validators();
    $estado = atom("ServiceForm");
    $services = atom([]);
    $especialistas = atom([]);
    $userName = atom("Usuario");
    $bookingDraft = map({
      serviceId: null,
      service: null,
      dia: /* @__PURE__ */ new Date(),
      especialista: null,
      hora: null
    });
    $pagination = map({
      current: 1,
      pageSize: 2,
      totalPages: 0
    });
    $uiState = map({
      loading: false,
      error: null
    });
    $mes = atom(/* @__PURE__ */ new Date());
    $totalEspecialistas = atom(0);
    loadUserAction = async () => {
      const user = await getCurrentUser();
      if (user?.nombre) {
        $userName.set(user.nombre);
      }
    };
    loadServicesAction = async () => {
      loadUserAction();
      const servicesData = await getServices();
      console.log("Servicios cargados:", servicesData);
      $services.set(servicesData.servicios || []);
    };
    selectServiceAction = async (service) => {
      $bookingDraft.setKey("service", service);
      $bookingDraft.setKey("serviceId", service.id);
      await loadEspecialistasAction();
      $estado.set("DateForm");
    };
    setMesAction = (fecha) => {
      $mes.set(fecha);
    };
    setDiaAction = async (dia) => {
      $bookingDraft.setKey("dia", dia);
      $mes.set(dia);
      $pagination.setKey("current", 1);
      await loadEspecialistasAction();
    };
    loadEspecialistasAction = async (page = null) => {
      const draft = $bookingDraft.get();
      const pagination = $pagination.get();
      const currentPage = page ?? pagination.current;
      if (!draft.service) {
        console.log("No hay servicio seleccionado");
        return;
      }
      if (!draft.service.id) {
        console.warn("El servicio seleccionado no tiene ID");
        return;
      }
      if (!draft.dia) {
        console.warn("No hay fecha seleccionada");
        return;
      }
      const fechaFormateada = formatearFechaISO(draft.dia);
      const offset = (currentPage - 1) * pagination.pageSize;
      const response = await getEspecialistasDisponibles(
        draft.service.id,
        fechaFormateada,
        pagination.pageSize,
        offset
      );
      $especialistas.set(response.data || []);
      $totalEspecialistas.set(response.total || 0);
      const totalPages = Math.ceil((response.total || 0) / pagination.pageSize);
      $pagination.setKey("current", currentPage);
      $pagination.setKey("totalPages", totalPages);
    };
    selectEspecialistaAction = (especialista, hora) => {
      $bookingDraft.setKey("especialista", especialista);
      $bookingDraft.setKey("hora", hora);
      console.log("Selecci\xF3n guardada:", { especialista, hora });
    };
    changePageAction = async (page) => {
      await loadEspecialistasAction(page);
    };
    confirmReservaAction = async () => {
      const draft = $bookingDraft.get();
      if (!draft.service?.id || !draft.especialista?.id_especialista || !draft.dia || !draft.hora) {
        $uiState.setKey("error", "Faltan datos requeridos para completar la reserva");
        return;
      }
      $uiState.setKey("loading", true);
      $uiState.setKey("error", null);
      let reservaExitosa = false;
      try {
        const userBookings = await getUserBookings();
        const targetDate = formatearFechaISO(draft.dia);
        if (hasWeeklyBookingForService(userBookings, draft.service.id, targetDate)) {
          throw new Error("Ya tienes una reserva de este servicio en esta semana");
        }
        if (exceedsWeeklyHoursLimit(userBookings, targetDate, draft.service.duracion_minutos || 60)) {
          throw new Error("Ya has alcanzado el m\xE1ximo de 40 horas permitidas por ley para esta semana");
        }
        const reservaData = {
          servicio_id: draft.service.id,
          especialista_id: draft.especialista.id_especialista,
          fecha: targetDate,
          hora: draft.hora,
          duracion: draft.service.duracion_minutos
        };
        await createReserva(reservaData);
        reservaExitosa = true;
      } catch (err) {
        $uiState.setKey("error", err.message);
      } finally {
        $uiState.setKey("loading", false);
      }
      if (reservaExitosa) {
        setTimeout(() => {
          resetBookingAction();
          globalThis.location.href = "/user/reservas";
        }, 800);
      }
    };
    resetBookingAction = () => {
      $estado.set("ServiceForm");
      $bookingDraft.set({
        serviceId: null,
        service: null,
        dia: /* @__PURE__ */ new Date(),
        especialista: null,
        hora: null
      });
      $mes.set(/* @__PURE__ */ new Date());
      $especialistas.set([]);
      $totalEspecialistas.set(0);
      $pagination.set({
        current: 1,
        pageSize: 2,
        totalPages: 0
      });
      $uiState.set({
        loading: false,
        error: null
      });
    };
  }
});

// src/js/user/bookings/components/Calendario.jsx
var DAYS_OF_WEEK, CALENDAR_WIDTH, CELL_WIDTH, CELL_HEIGHT, FIRST_DAY_OF_WEEK, Calendario, buildCalendarDays, renderNavigationButtons, isPreviousMonthDisabled, renderWeeks, renderCell, isDaySelected, isDayInPast;
var init_Calendario = __esm({
  "src/js/user/bookings/components/Calendario.jsx"() {
    init_preact_module();
    DAYS_OF_WEEK = ["L", "M", "X", "J", "V", "S", "D"];
    CALENDAR_WIDTH = "380px";
    CELL_WIDTH = "40px";
    CELL_HEIGHT = "40px";
    FIRST_DAY_OF_WEEK = 7;
    Calendario = ({ fecha, diaSeleccionado, handleDiaChange, handleMesChange }) => {
      if (!fecha || !handleDiaChange || !handleMesChange) return null;
      const year = fecha.getFullYear();
      const month = fecha.getMonth();
      const textMonth = fecha.toLocaleString("es-ES", { month: "long" });
      const textYear = year;
      const diasTotalesMes = new Date(year, month + 1, 0).getDate();
      const primerDiaMes = (new Date(year, month, 1).getDay() || FIRST_DAY_OF_WEEK) - 1;
      const calendarioCompleto = buildCalendarDays(primerDiaMes, diasTotalesMes);
      return /* @__PURE__ */ _(
        "div",
        {
          class: "card border-0 shadow-sm rounded-4 p-4 bg-white",
          style: `width: ${CALENDAR_WIDTH}; margin: 0 auto;`
        },
        renderNavigationButtons(year, month, textMonth, textYear, handleMesChange),
        /* @__PURE__ */ _("div", { class: "row g-0 text-center small text-primary fw-bold mb-2" }, DAYS_OF_WEEK.map((d3) => /* @__PURE__ */ _("div", { class: "col" }, d3))),
        renderWeeks(calendarioCompleto, year, month, diaSeleccionado, handleDiaChange)
      );
    };
    buildCalendarDays = (primerDiaMes, diasTotalesMes) => {
      const out = [];
      for (let i3 = 0; i3 < primerDiaMes; i3++) out.push(null);
      for (let i3 = 1; i3 <= diasTotalesMes; i3++) out.push(i3);
      while (out.length % 7 !== 0) out.push(null);
      return out;
    };
    renderNavigationButtons = (year, month, textMonth, textYear, handleMesChange) => {
      const today = /* @__PURE__ */ new Date();
      const isPreviousDisabled = isPreviousMonthDisabled(year, month, today);
      const prev = () => handleMesChange(new Date(year, month - 1));
      const next = () => handleMesChange(new Date(year, month + 1));
      return /* @__PURE__ */ _("div", { class: "d-flex justify-content-between align-items-center mb-4 px-2" }, /* @__PURE__ */ _(
        "button",
        {
          class: "btn btn-sm btn-link text-decoration-none text-dark p-0",
          disabled: isPreviousDisabled,
          onClick: prev,
          "aria-label": "Mes anterior"
        },
        /* @__PURE__ */ _("i", { class: "bi bi-chevron-left" })
      ), /* @__PURE__ */ _("div", { class: "fw-bold text-capitalize fs-5" }, textMonth, " ", textYear), /* @__PURE__ */ _(
        "button",
        {
          class: "btn btn-sm btn-link text-decoration-none text-dark p-0",
          onClick: next,
          "aria-label": "Mes siguiente"
        },
        /* @__PURE__ */ _("i", { class: "bi bi-chevron-right" })
      ));
    };
    isPreviousMonthDisabled = (year, month, today) => {
      if (year < today.getFullYear()) return true;
      if (year === today.getFullYear() && month <= today.getMonth()) return true;
      return false;
    };
    renderWeeks = (calendarioCompleto, year, month, diaSeleccionado, handleDiaChange) => {
      const weeks = [];
      for (let i3 = 0; i3 < calendarioCompleto.length; i3 += 7) {
        weeks.push(calendarioCompleto.slice(i3, i3 + 7));
      }
      return weeks.map((week) => /* @__PURE__ */ _("div", { class: "row g-0 text-center mb-2" }, week.map((d3) => /* @__PURE__ */ _("div", { class: "col d-flex justify-content-center" }, renderCell(d3, year, month, diaSeleccionado, handleDiaChange)))));
    };
    renderCell = (dia, year, month, diaSeleccionado, handleDiaChange) => {
      if (!dia) return /* @__PURE__ */ _("div", { style: `width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};` });
      const isSelected = isDaySelected(dia, month, year, diaSeleccionado);
      const isPast = isDayInPast(dia, month, year);
      const isSunday = new Date(year, month, dia).getDay() === 0;
      if (isPast || isSunday) {
        return /* @__PURE__ */ _(
          "span",
          {
            class: "d-flex align-items-center justify-content-center text-secondary",
            style: `width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};`,
            "aria-label": `D\xEDa ${dia} no disponible`
          },
          dia
        );
      }
      if (isSelected) {
        return /* @__PURE__ */ _(
          "button",
          {
            type: "button",
            class: "btn rounded-circle d-flex align-items-center justify-content-center bg-primary text-white border-0",
            style: `width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};`,
            onClick: () => handleDiaChange(new Date(year, month, dia)),
            "aria-label": `D\xEDa ${dia}`
          },
          dia
        );
      }
      return /* @__PURE__ */ _(
        "button",
        {
          type: "button",
          class: "btn rounded-circle d-flex align-items-center justify-content-center text-black border-0",
          style: `width: ${CELL_WIDTH}; height: ${CELL_HEIGHT};`,
          onClick: () => handleDiaChange(new Date(year, month, dia)),
          "aria-label": `D\xEDa ${dia}`
        },
        dia
      );
    };
    isDaySelected = (dia, month, year, diaSeleccionado) => {
      if (!diaSeleccionado) return false;
      return dia === diaSeleccionado.getDate() && month === diaSeleccionado.getMonth() && year === diaSeleccionado.getFullYear();
    };
    isDayInPast = (dia, month, year) => {
      const fecha = new Date(year, month, dia);
      const hoy = /* @__PURE__ */ new Date();
      hoy.setHours(0, 0, 0, 0);
      return fecha < hoy;
    };
  }
});

// src/js/user/bookings/components/Pagination.jsx
var handlePreviousPage, handleNextPage, renderPageNumbers, Pagination;
var init_Pagination = __esm({
  "src/js/user/bookings/components/Pagination.jsx"() {
    init_preact_module();
    handlePreviousPage = (currentPage, onPageChange) => {
      if (currentPage > 1) {
        onPageChange(currentPage - 1);
      }
    };
    handleNextPage = (currentPage, totalPages, onPageChange) => {
      if (currentPage < totalPages) {
        onPageChange(currentPage + 1);
      }
    };
    renderPageNumbers = (totalPages, currentPage, onPageChange) => {
      const pages = [];
      for (let i3 = 1; i3 <= totalPages; i3++) {
        pages.push(
          /* @__PURE__ */ _("li", { class: `page-item ${currentPage === i3 ? "active" : ""}` }, /* @__PURE__ */ _("button", { class: "page-link", onClick: () => onPageChange(i3), disabled: currentPage === i3 }, i3))
        );
      }
      return pages;
    };
    Pagination = ({ currentPage, totalPages, onPageChange }) => {
      if (totalPages <= 1) {
        return null;
      }
      return /* @__PURE__ */ _("nav", { "aria-label": "Pagination" }, /* @__PURE__ */ _("ul", { class: "pagination justify-content-center" }, /* @__PURE__ */ _("li", { class: `page-item ${currentPage === 1 ? "disabled" : ""}` }, /* @__PURE__ */ _(
        "button",
        {
          class: "page-link",
          onClick: () => handlePreviousPage(currentPage, onPageChange),
          disabled: currentPage === 1,
          "aria-label": "Previous"
        },
        /* @__PURE__ */ _("span", { "aria-hidden": "true" }, "\xAB"),
        /* @__PURE__ */ _("span", { class: "visually-hidden" }, "Anterior")
      )), renderPageNumbers(totalPages, currentPage, onPageChange), /* @__PURE__ */ _("li", { class: `page-item ${currentPage === totalPages ? "disabled" : ""}` }, /* @__PURE__ */ _(
        "button",
        {
          class: "page-link",
          onClick: () => handleNextPage(currentPage, totalPages, onPageChange),
          disabled: currentPage === totalPages,
          "aria-label": "Next"
        },
        /* @__PURE__ */ _("span", { "aria-hidden": "true" }, "\xBB"),
        /* @__PURE__ */ _("span", { class: "visually-hidden" }, "Siguiente")
      ))));
    };
  }
});

// src/js/user/bookings/components/EspecialistasList.jsx
var MIN_PHOTO_HEIGHT, PHOTO_OBJECT_FIT, PHOTO_OBJECT_POSITION, renderEmptyState, renderSpecialistPhoto, renderTimeButton, renderAvailableTimes, renderSpecialistCard, EspecialistasList;
var init_EspecialistasList = __esm({
  "src/js/user/bookings/components/EspecialistasList.jsx"() {
    init_preact_module();
    init_Pagination();
    init_formatters();
    MIN_PHOTO_HEIGHT = "150px";
    PHOTO_OBJECT_FIT = "cover";
    PHOTO_OBJECT_POSITION = "top center";
    renderEmptyState = () => /* @__PURE__ */ _("div", { class: "text-center py-5" }, /* @__PURE__ */ _("i", { class: "bi bi-calendar-x fs-1 text-muted" }), /* @__PURE__ */ _("p", { class: "text-muted mt-2" }, "No hay especialistas disponibles para esta fecha"));
    renderSpecialistPhoto = (fotoUrl, nombre) => {
      if (fotoUrl) {
        return /* @__PURE__ */ _(
          "img",
          {
            src: fotoUrl,
            alt: nombre,
            class: "img-fluid w-100 h-100",
            style: `object-fit: ${PHOTO_OBJECT_FIT}; object-position: ${PHOTO_OBJECT_POSITION}; min-height: ${MIN_PHOTO_HEIGHT};`
          }
        );
      }
      return /* @__PURE__ */ _(
        "div",
        {
          class: "w-100 h-100 bg-light d-flex align-items-center justify-content-center text-secondary",
          style: `min-height: ${MIN_PHOTO_HEIGHT};`
        },
        /* @__PURE__ */ _("i", { class: "bi bi-person-fill fs-1" })
      );
    };
    renderTimeButton = (hora, isSelected, timeHasPassed, onSelectHora, especialista) => {
      const buttonClass = isSelected ? "btn btn-primary btn-sm px-3" : timeHasPassed ? "btn btn-outline-secondary btn-sm px-3 text-muted" : "btn btn-outline-primary btn-sm px-3";
      const buttonStyle = timeHasPassed ? "cursor: not-allowed; opacity: 0.5;" : "";
      return /* @__PURE__ */ _(
        "button",
        {
          class: buttonClass,
          onClick: () => !timeHasPassed && onSelectHora(especialista, hora),
          disabled: timeHasPassed,
          title: timeHasPassed ? "Esta hora ya ha pasado" : "",
          style: buttonStyle,
          "aria-label": `Seleccionar hora ${hora}`
        },
        hora,
        timeHasPassed ? /* @__PURE__ */ _("i", { class: "bi bi-lock-fill ms-1 small" }) : ""
      );
    };
    renderAvailableTimes = (horasDisponibles, selectedEspecialista, selectedHora, diaSeleccionado, onSelectHora, especialista) => {
      return /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("strong", { class: "d-block small text-secondary mb-2" }, "Horarios disponibles:"), /* @__PURE__ */ _("div", { class: "d-flex flex-wrap gap-2" }, horasDisponibles.map((hora) => {
        const isSelected = selectedEspecialista && selectedHora && selectedEspecialista.id_especialista === especialista.id_especialista && selectedHora === hora;
        const timeHasPassed = diaSeleccionado && isPastTime(diaSeleccionado, hora);
        return renderTimeButton(hora, isSelected, timeHasPassed, onSelectHora, especialista);
      })));
    };
    renderSpecialistCard = (especialista, selectedEspecialista, selectedHora, diaSeleccionado, onSelectHora) => {
      return /* @__PURE__ */ _("div", { class: "card border border-0 shadow-sm overflow-hidden" }, /* @__PURE__ */ _("div", { class: "row g-0" }, /* @__PURE__ */ _("div", { class: "col-4 col-sm-3 col-md-2 p-0 position-relative" }, renderSpecialistPhoto(especialista.foto_url, especialista.nombre)), /* @__PURE__ */ _("div", { class: "col-8 col-sm-9 col-md-10" }, /* @__PURE__ */ _("div", { class: "card-body" }, /* @__PURE__ */ _("h5", { class: "card-title fw-bold" }, especialista.nombre, " ", especialista.apellidos), /* @__PURE__ */ _("p", { class: "card-text text-muted small mb-3" }, especialista.descripcion), renderAvailableTimes(
        especialista.horas_disponibles,
        selectedEspecialista,
        selectedHora,
        diaSeleccionado,
        onSelectHora,
        especialista
      )))));
    };
    EspecialistasList = ({
      especialistas,
      onSelectHora,
      selectedEspecialista,
      selectedHora,
      currentPage,
      totalPages,
      onPageChange,
      diaSeleccionado
    }) => {
      if (especialistas.length === 0) {
        return renderEmptyState();
      }
      return /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("h5", { class: "card-title mb-3" }, "Especialistas disponibles"), /* @__PURE__ */ _("div", { class: "d-flex flex-column gap-3 mb-4" }, especialistas.map(
        (especialista) => renderSpecialistCard(
          especialista,
          selectedEspecialista,
          selectedHora,
          diaSeleccionado,
          onSelectHora
        )
      )), /* @__PURE__ */ _(Pagination, { currentPage, totalPages, onPageChange }));
    };
  }
});

// src/js/user/bookings/routes/dateForm.jsx
var DateForm;
var init_dateForm = __esm({
  "src/js/user/bookings/routes/dateForm.jsx"() {
    init_preact_module();
    init_preact();
    init_Calendario();
    init_EspecialistasList();
    init_bookingsStore();
    DateForm = () => {
      const booking = useStore($bookingDraft);
      const especialistas = useStore($especialistas);
      const pagination = useStore($pagination);
      const mes = useStore($mes);
      return /* @__PURE__ */ _("div", { class: "row g-4" }, /* @__PURE__ */ _("div", { class: "col-12 col-lg-5" }, /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("div", { class: "d-flex justify-content-center" }, /* @__PURE__ */ _(
        Calendario,
        {
          fecha: mes,
          diaSeleccionado: booking.dia,
          handleMesChange: setMesAction,
          handleDiaChange: setDiaAction
        }
      ))))), /* @__PURE__ */ _("div", { class: "col-12 col-lg-7" }, /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("div", null, /* @__PURE__ */ _(
        EspecialistasList,
        {
          especialistas,
          onSelectHora: selectEspecialistaAction,
          selectedEspecialista: booking.especialista,
          selectedHora: booking.hora,
          currentPage: pagination.current,
          totalPages: pagination.totalPages,
          onPageChange: changePageAction,
          diaSeleccionado: booking.dia
        }
      )))));
    };
  }
});

// src/js/user/bookings/components/service.jsx
var MIN_HEIGHT, SHADOW_COLOR, handleServiceClick, Service;
var init_service = __esm({
  "src/js/user/bookings/components/service.jsx"() {
    init_preact_module();
    MIN_HEIGHT = "120px";
    SHADOW_COLOR = "rgba(13, 110, 253, 0.5)";
    handleServiceClick = (service, onSelect) => {
      if (onSelect) {
        onSelect(service);
      }
    };
    Service = ({ service, onSelect, isSelected = false }) => {
      return /* @__PURE__ */ _(
        "div",
        {
          class: `card ${isSelected ? "border-primary border-3" : ""}`,
          onClick: () => handleServiceClick(service, onSelect),
          style: `cursor: pointer; min-height: ${MIN_HEIGHT}; ${isSelected ? `box-shadow: 0 0 10px ${SHADOW_COLOR};` : ""}`
        },
        /* @__PURE__ */ _("div", { class: "card-body d-flex align-items-center gap-3" }, /* @__PURE__ */ _("i", { class: "bi bi-scissors fs-1 text-primary" }), /* @__PURE__ */ _("div", { class: "flex-grow-1" }, /* @__PURE__ */ _("h5", { class: "card-title mb-2" }, service.nombre_servicio), /* @__PURE__ */ _("p", { class: "card-text text-muted mb-0" }, /* @__PURE__ */ _("i", { class: "bi bi-clock me-1" }), service.duracion_minutos, " minutos")))
      );
    };
  }
});

// src/js/user/bookings/routes/serviceForm.jsx
var ServiceForm;
var init_serviceForm = __esm({
  "src/js/user/bookings/routes/serviceForm.jsx"() {
    init_preact_module();
    init_preact();
    init_service();
    init_bookingsStore();
    ServiceForm = () => {
      const services = useStore($services);
      const booking = useStore($bookingDraft);
      return /* @__PURE__ */ _("div", { class: "container-fluid p-3 d-flex flex-column align-items-center" }, /* @__PURE__ */ _("div", { class: "row g-3" }, services.map((service) => /* @__PURE__ */ _("div", { class: "col-12 col-sm-6 col-md-4 col-lg-3" }, /* @__PURE__ */ _(
        Service,
        {
          service,
          onSelect: selectServiceAction,
          isSelected: booking.serviceId === service.id
        }
      )))));
    };
  }
});

// src/js/user/bookings/hooks/useReservas.js
var useReservas;
var init_useReservas = __esm({
  "src/js/user/bookings/hooks/useReservas.js"() {
    init_preact();
    init_bookingsStore();
    useReservas = () => {
      const booking = useStore($bookingDraft);
      const uiState = useStore($uiState);
      return {
        selectedService: booking.service,
        dia: booking.dia,
        selectedEspecialista: booking.especialista,
        selectedHora: booking.hora,
        loading: uiState.loading,
        error: uiState.error,
        confirmarReserva: confirmReservaAction
      };
    };
  }
});

// src/js/user/bookings/components/ResumenItem.jsx
var ICON_BOX_SIZE, BACKGROUND_COLOR, ICON_COLOR, ICON_SIZE, TEXT_COLOR, ResumenItem;
var init_ResumenItem = __esm({
  "src/js/user/bookings/components/ResumenItem.jsx"() {
    init_preact_module();
    ICON_BOX_SIZE = "48px";
    BACKGROUND_COLOR = "#fce7f3";
    ICON_COLOR = "#e83e8c";
    ICON_SIZE = "24px";
    TEXT_COLOR = "#2d3748";
    ResumenItem = ({ icon, label, value }) => {
      return /* @__PURE__ */ _("div", { class: "d-flex gap-3 mb-4" }, /* @__PURE__ */ _(
        "div",
        {
          class: "d-flex align-items-center justify-content-center rounded-3",
          style: `width: ${ICON_BOX_SIZE}; height: ${ICON_BOX_SIZE}; background-color: ${BACKGROUND_COLOR}; flex-shrink: 0;`
        },
        /* @__PURE__ */ _("i", { class: `bi bi-${icon}`, style: `font-size: ${ICON_SIZE}; color: ${ICON_COLOR};` })
      ), /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("p", { class: "text-muted small mb-1" }, label), /* @__PURE__ */ _("p", { class: "fw-semibold mb-0", style: `color: ${TEXT_COLOR};` }, value)));
    };
  }
});

// src/js/user/bookings/components/ResumenCita.jsx
var BACKGROUND_COLOR2, TEXT_COLOR2, ResumenCita;
var init_ResumenCita = __esm({
  "src/js/user/bookings/components/ResumenCita.jsx"() {
    init_preact_module();
    init_ResumenItem();
    init_formatters();
    BACKGROUND_COLOR2 = "#f8f9fa";
    TEXT_COLOR2 = "#2d3748";
    ResumenCita = ({ selectedService, selectedEspecialista, dia, selectedHora }) => {
      const fechaCapitalizada = formatearFechaLarga(dia);
      return /* @__PURE__ */ _(
        "div",
        {
          class: "card border-0 shadow-sm rounded-4 p-4",
          style: `background-color: ${BACKGROUND_COLOR2};`
        },
        /* @__PURE__ */ _("h5", { class: "fw-bold mb-4", style: `color: ${TEXT_COLOR2};` }, "Resumen de la Cita"),
        /* @__PURE__ */ _(
          ResumenItem,
          {
            icon: "scissors",
            label: "Servicio",
            value: selectedService?.nombre || selectedService?.nombre_servicio
          }
        ),
        /* @__PURE__ */ _(
          ResumenItem,
          {
            icon: "person",
            label: "Especialista",
            value: `${selectedEspecialista?.nombre} ${selectedEspecialista?.apellidos}`
          }
        ),
        /* @__PURE__ */ _(
          ResumenItem,
          {
            icon: "calendar-event",
            label: "Fecha y Hora",
            value: `${fechaCapitalizada} a las ${selectedHora || "..."}`
          }
        ),
        /* @__PURE__ */ _("div", { class: "d-flex gap-3" }, /* @__PURE__ */ _(
          ResumenItem,
          {
            icon: "clock",
            label: "Duraci\xF3n",
            value: `Aprox. ${selectedService?.duracion_minutos || 0} min`
          }
        ))
      );
    };
  }
});

// src/js/user/bookings/components/StatusAlert.jsx
var renderLoadingAlert, renderErrorAlert, StatusAlert;
var init_StatusAlert = __esm({
  "src/js/user/bookings/components/StatusAlert.jsx"() {
    init_preact_module();
    renderLoadingAlert = () => /* @__PURE__ */ _("div", { class: "alert alert-info d-flex align-items-center", role: "alert" }, /* @__PURE__ */ _("div", { class: "spinner-border spinner-border-sm me-2", role: "status" }, /* @__PURE__ */ _("span", { class: "visually-hidden" }, "Loading...")), /* @__PURE__ */ _("div", null, "Confirming your booking..."));
    renderErrorAlert = (error) => /* @__PURE__ */ _("div", { class: "alert alert-danger", role: "alert" }, /* @__PURE__ */ _("i", { class: "bi bi-exclamation-triangle me-2" }), error);
    StatusAlert = ({ loading, error }) => {
      if (loading) {
        return renderLoadingAlert();
      }
      if (error) {
        return renderErrorAlert(error);
      }
      return null;
    };
  }
});

// src/js/user/bookings/components/ConfirmationActions.jsx
var BUTTON_PADDING, BUTTON_PADDING_CONFIRM, BORDER_COLOR, TEXT_COLOR3, PRIMARY_COLOR, ConfirmationActions;
var init_ConfirmationActions = __esm({
  "src/js/user/bookings/components/ConfirmationActions.jsx"() {
    init_preact_module();
    BUTTON_PADDING = "px-4";
    BUTTON_PADDING_CONFIRM = "px-5";
    BORDER_COLOR = "#cbd5e0";
    TEXT_COLOR3 = "#4a5568";
    PRIMARY_COLOR = "#e83e8c";
    ConfirmationActions = ({ loading, onModificar, onConfirmar }) => {
      return /* @__PURE__ */ _("div", { class: "d-flex gap-3 justify-content-end flex-wrap" }, /* @__PURE__ */ _(
        "button",
        {
          class: `btn btn-outline-secondary rounded-pill ${BUTTON_PADDING}`,
          onClick: onModificar,
          disabled: loading,
          style: `border-color: ${BORDER_COLOR}; color: ${TEXT_COLOR3};`
        },
        "Modificar selecci\xF3n"
      ), /* @__PURE__ */ _(
        "button",
        {
          class: `btn rounded-pill ${BUTTON_PADDING_CONFIRM}`,
          onClick: onConfirmar,
          disabled: loading,
          style: `background-color: ${PRIMARY_COLOR}; border: none; color: white; font-weight: 600;`
        },
        loading ? /* @__PURE__ */ _("span", null, /* @__PURE__ */ _("span", { class: "spinner-border spinner-border-sm me-2", role: "status" }), "Confirmando...") : "Confirmar Reserva"
      ));
    };
  }
});

// src/js/user/bookings/routes/confirmationForm.jsx
var ConfirmationForm;
var init_confirmationForm = __esm({
  "src/js/user/bookings/routes/confirmationForm.jsx"() {
    init_preact_module();
    init_preact();
    init_bookingsStore();
    init_useReservas();
    init_ResumenCita();
    init_StatusAlert();
    init_ConfirmationActions();
    ConfirmationForm = () => {
      const userName = useStore($userName);
      const {
        selectedService,
        dia,
        selectedEspecialista,
        selectedHora,
        loading,
        error,
        confirmarReserva
      } = useReservas();
      const handleModificar = () => {
        $estado.set("DateForm");
      };
      return /* @__PURE__ */ _("div", { class: "container py-4" }, /* @__PURE__ */ _("div", { class: "mb-4" }, /* @__PURE__ */ _("h2", { class: "fw-bold mb-2", style: "color: #2d3748;" }, "Confirma tu Cita"), /* @__PURE__ */ _("p", { class: "text-muted" }, "Revisa los detalles de tu reserva antes de confirmar.")), /* @__PURE__ */ _("div", { class: "row g-4" }, /* @__PURE__ */ _("div", { class: "col-12 col-lg-5" }, /* @__PURE__ */ _(
        ResumenCita,
        {
          selectedService,
          selectedEspecialista,
          dia,
          selectedHora
        }
      )), /* @__PURE__ */ _("div", { class: "col-12 col-lg-7" }, /* @__PURE__ */ _("div", { class: "card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-between" }, /* @__PURE__ */ _("div", null, /* @__PURE__ */ _("h5", { class: "fw-bold mb-3", style: "color: #2d3748;" }, "Todo listo, ", userName), /* @__PURE__ */ _("p", { class: "text-muted mb-4" }, "Solo falta un paso para confirmar tu cita. Tus datos se rellenar\xE1n autom\xE1ticamente."), /* @__PURE__ */ _(StatusAlert, { loading, error })), /* @__PURE__ */ _(
        ConfirmationActions,
        {
          loading,
          onModificar: handleModificar,
          onConfirmar: confirmarReserva
        }
      )))));
    };
  }
});

// src/js/user/bookings/components/BookingNavigation.jsx
var BUTTON_SIZE, SPACER_SIZE, Z_INDEX, getStepInfo, handlePrevStep, handleNextStep, canNavigateNext, renderPrevButton, renderStepInfo, renderNextButtonOrSpacer, BookingNavigation;
var init_BookingNavigation = __esm({
  "src/js/user/bookings/components/BookingNavigation.jsx"() {
    init_preact_module();
    init_preact();
    init_bookingsStore();
    BUTTON_SIZE = "40px";
    SPACER_SIZE = "120px";
    Z_INDEX = 1e3;
    getStepInfo = (currentState) => {
      switch (currentState) {
        case "ServiceForm":
          return { number: 1, title: "Servicios" };
        case "DateForm":
          return { number: 2, title: "Fecha y Especialista" };
        case "ConfirmationForm":
          return { number: 3, title: "Confirmaci\xF3n" };
        default:
          return { number: 0, title: "" };
      }
    };
    handlePrevStep = (estado) => {
      if (estado === "DateForm") $estado.set("ServiceForm");
      if (estado === "ConfirmationForm") $estado.set("DateForm");
    };
    handleNextStep = (estado) => {
      if (estado === "ServiceForm") $estado.set("DateForm");
      if (estado === "DateForm") $estado.set("ConfirmationForm");
    };
    canNavigateNext = (estado, booking) => {
      return estado === "ServiceForm" && booking.service || estado === "DateForm" && booking.especialista && booking.hora;
    };
    renderPrevButton = (canPrev, handlePrev) => /* @__PURE__ */ _(
      "button",
      {
        class: "btn btn-light rounded-circle text-secondary",
        style: `width: ${BUTTON_SIZE}; height: ${BUTTON_SIZE};`,
        onClick: handlePrev,
        disabled: !canPrev,
        "aria-label": "Paso anterior"
      },
      /* @__PURE__ */ _("i", { class: "bi bi-chevron-left" })
    );
    renderStepInfo = (stepInfo) => /* @__PURE__ */ _("div", { class: "text-center", style: `min-width: ${SPACER_SIZE};` }, /* @__PURE__ */ _(
      "div",
      {
        class: "text-xs text-muted fw-bold text-uppercase",
        style: "font-size: 0.7rem; letter-spacing: 1px;"
      },
      "Paso ",
      stepInfo.number,
      "/3"
    ), /* @__PURE__ */ _("div", { class: "fw-bold fs-6" }, stepInfo.title));
    renderNextButtonOrSpacer = (estado, canNext, handleNext) => {
      if (estado !== "ConfirmationForm") {
        return /* @__PURE__ */ _(
          "button",
          {
            class: "btn btn-primary rounded-circle",
            style: `width: ${BUTTON_SIZE}; height: ${BUTTON_SIZE};`,
            onClick: handleNext,
            disabled: !canNext,
            "aria-label": "Siguiente paso"
          },
          /* @__PURE__ */ _("i", { class: "bi bi-chevron-right" })
        );
      }
      return /* @__PURE__ */ _("div", { style: `width: ${BUTTON_SIZE};` });
    };
    BookingNavigation = () => {
      const estado = useStore($estado);
      const booking = useStore($bookingDraft);
      const canPrev = estado !== "ServiceForm";
      const canNext = canNavigateNext(estado, booking);
      const stepInfo = getStepInfo(estado);
      return /* @__PURE__ */ _(
        "div",
        {
          class: "position-sticky bottom-0 pb-4 d-flex justify-content-center",
          style: `z-index: ${Z_INDEX}; pointer-events: none;`
        },
        /* @__PURE__ */ _(
          "div",
          {
            class: "bg-white shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-4 border",
            style: "pointer-events: auto;"
          },
          renderPrevButton(canPrev, () => handlePrevStep(estado)),
          " ",
          renderStepInfo(stepInfo),
          renderNextButtonOrSpacer(estado, canNext, () => handleNextStep(estado))
        )
      );
    };
  }
});

// src/js/user/bookings/bookingsApp.jsx
var require_bookingsApp = __commonJS({
  "src/js/user/bookings/bookingsApp.jsx"() {
    init_preact_module();
    init_hooks_module();
    init_preact();
    init_bookingsStore();
    init_dateForm();
    init_serviceForm();
    init_confirmationForm();
    init_BookingNavigation();
    init_bootstrap_esm();
    window.bootstrap = bootstrap_esm_exports;
    var BookingsApp = () => {
      const estado = useStore($estado);
      y2(() => {
        loadServicesAction();
      }, []);
      return /* @__PURE__ */ _("div", { class: "d-flex flex-column position-relative", style: "min-height: 75vh;" }, /* @__PURE__ */ _("h1", { class: "h2 text-dark mb-5" }, "Nueva Reserva"), /* @__PURE__ */ _("div", { class: "flex-grow-1 mb-5" }, renderCurrentStep(estado)), /* @__PURE__ */ _(BookingNavigation, null));
    };
    var renderCurrentStep = (estado) => {
      if (!estado) {
        return /* @__PURE__ */ _("div", { class: "alert alert-warning" }, "Cargando...");
      }
      if (estado === "ConfirmationForm") {
        return /* @__PURE__ */ _(ConfirmationForm, null);
      }
      if (estado === "DateForm") {
        return /* @__PURE__ */ _(DateForm, null);
      }
      return /* @__PURE__ */ _(ServiceForm, null);
    };
    G(/* @__PURE__ */ _(BookingsApp, null), document.getElementById("bookings-app"));
  }
});
export default require_bookingsApp();
//# sourceMappingURL=bookings.js.map
