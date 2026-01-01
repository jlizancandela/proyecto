import{a as g,b as mr}from"../../chunk-5KL6F2HG.js";function P(e,t){for(var r in t)e[r]=t[r];return e}function we(e){e&&e.parentNode&&e.parentNode.removeChild(e)}function p(e,t,r){var o,i,n,s={};for(n in t)n=="key"?o=t[n]:n=="ref"?i=t[n]:s[n]=t[n];if(arguments.length>2&&(s.children=arguments.length>3?ce.call(arguments,2):r),typeof e=="function"&&e.defaultProps!=null)for(n in e.defaultProps)s[n]===void 0&&(s[n]=e.defaultProps[n]);return ie(e,s,o,i,null)}function ie(e,t,r,o,i){var n={type:e,props:t,key:r,ref:o,__k:null,__:null,__b:0,__e:null,__c:null,constructor:void 0,__v:i??++We,__i:-1,__u:0};return i==null&&h.vnode!=null&&h.vnode(n),n}function _e(e){return e.children}function se(e,t){this.props=e,this.context=t}function z(e,t){if(t==null)return e.__?z(e.__,e.__i+1):null;for(var r;t<e.__k.length;t++)if((r=e.__k[t])!=null&&r.__e!=null)return r.__e;return typeof e.type=="function"?z(e):null}function Ge(e){var t,r;if((e=e.__)!=null&&e.__c!=null){for(e.__e=e.__c.base=null,t=0;t<e.__k.length;t++)if((r=e.__k[t])!=null&&r.__e!=null){e.__e=e.__c.base=r.__e;break}return Ge(e)}}function Ue(e){(!e.__d&&(e.__d=!0)&&U.push(e)&&!ae.__r++||Me!=h.debounceRendering)&&((Me=h.debounceRendering)||Ke)(ae)}function ae(){for(var e,t,r,o,i,n,s,c=1;U.length;)U.length>c&&U.sort(ze),e=U.shift(),c=U.length,e.__d&&(r=void 0,o=void 0,i=(o=(t=e).__v).__e,n=[],s=[],t.__P&&((r=P({},o)).__v=o.__v+1,h.vnode&&h.vnode(r),ke(t.__P,r,o,t.__n,t.__P.namespaceURI,32&o.__u?[i]:null,n,i??z(o),!!(32&o.__u),s),r.__v=o.__v,r.__.__k[r.__i]=r,Je(n,r,s),o.__e=o.__=null,r.__e!=i&&Ge(r)));ae.__r=0}function qe(e,t,r,o,i,n,s,c,_,l,u){var a,d,f,x,T,k,b,m=o&&o.__k||Ve,O=t.length;for(_=gr(r,t,m,_,O),a=0;a<O;a++)(f=r.__k[a])!=null&&(d=f.__i==-1?q:m[f.__i]||q,f.__i=a,k=ke(e,f,d,i,n,s,c,_,l,u),x=f.__e,f.ref&&d.ref!=f.ref&&(d.ref&&Se(d.ref,null,f),u.push(f.ref,f.__c||x,f)),T==null&&x!=null&&(T=x),(b=!!(4&f.__u))||d.__k===f.__k?_=Xe(f,_,e,b):typeof f.type=="function"&&k!==void 0?_=k:x&&(_=x.nextSibling),f.__u&=-7);return r.__e=T,_}function gr(e,t,r,o,i){var n,s,c,_,l,u=r.length,a=u,d=0;for(e.__k=new Array(i),n=0;n<i;n++)(s=t[n])!=null&&typeof s!="boolean"&&typeof s!="function"?(typeof s=="string"||typeof s=="number"||typeof s=="bigint"||s.constructor==String?s=e.__k[n]=ie(null,s,null,null,null):le(s)?s=e.__k[n]=ie(_e,{children:s},null,null,null):s.constructor==null&&s.__b>0?s=e.__k[n]=ie(s.type,s.props,s.key,s.ref?s.ref:null,s.__v):e.__k[n]=s,_=n+d,s.__=e,s.__b=e.__b+1,c=null,(l=s.__i=yr(s,r,_,a))!=-1&&(a--,(c=r[l])&&(c.__u|=2)),c==null||c.__v==null?(l==-1&&(i>u?d--:i<u&&d++),typeof s.type!="function"&&(s.__u|=4)):l!=_&&(l==_-1?d--:l==_+1?d++:(l>_?d--:d++,s.__u|=4))):e.__k[n]=null;if(a)for(n=0;n<u;n++)(c=r[n])!=null&&(2&c.__u)==0&&(c.__e==o&&(o=z(c)),Qe(c,c));return o}function Xe(e,t,r,o){var i,n;if(typeof e.type=="function"){for(i=e.__k,n=0;i&&n<i.length;n++)i[n]&&(i[n].__=e,t=Xe(i[n],t,r,o));return t}e.__e!=t&&(o&&(t&&e.type&&!t.parentNode&&(t=z(e)),r.insertBefore(e.__e,t||null)),t=e.__e);do t=t&&t.nextSibling;while(t!=null&&t.nodeType==8);return t}function yr(e,t,r,o){var i,n,s,c=e.key,_=e.type,l=t[r],u=l!=null&&(2&l.__u)==0;if(l===null&&c==null||u&&c==l.key&&_==l.type)return r;if(o>(u?1:0)){for(i=r-1,n=r+1;i>=0||n<t.length;)if((l=t[s=i>=0?i--:n++])!=null&&(2&l.__u)==0&&c==l.key&&_==l.type)return s}return-1}function Be(e,t,r){t[0]=="-"?e.setProperty(t,r??""):e[t]=r==null?"":typeof r!="number"||br.test(t)?r:r+"px"}function ne(e,t,r,o,i){var n,s;e:if(t=="style")if(typeof r=="string")e.style.cssText=r;else{if(typeof o=="string"&&(e.style.cssText=o=""),o)for(t in o)r&&t in r||Be(e.style,t,"");if(r)for(t in r)o&&r[t]==o[t]||Be(e.style,t,r[t])}else if(t[0]=="o"&&t[1]=="n")n=t!=(t=t.replace(Ye,"$1")),s=t.toLowerCase(),t=s in e||t=="onFocusOut"||t=="onFocusIn"?s.slice(2):t.slice(2),e.l||(e.l={}),e.l[t+n]=r,r?o?r.u=o.u:(r.u=xe,e.addEventListener(t,n?ye:ge,n)):e.removeEventListener(t,n?ye:ge,n);else{if(i=="http://www.w3.org/2000/svg")t=t.replace(/xlink(H|:h)/,"h").replace(/sName$/,"s");else if(t!="width"&&t!="height"&&t!="href"&&t!="list"&&t!="form"&&t!="tabIndex"&&t!="download"&&t!="rowSpan"&&t!="colSpan"&&t!="role"&&t!="popover"&&t in e)try{e[t]=r??"";break e}catch{}typeof r=="function"||(r==null||r===!1&&t[4]!="-"?e.removeAttribute(t):e.setAttribute(t,t=="popover"&&r==1?"":r))}}function je(e){return function(t){if(this.l){var r=this.l[t.type+e];if(t.t==null)t.t=xe++;else if(t.t<r.u)return;return r(h.event?h.event(t):t)}}}function ke(e,t,r,o,i,n,s,c,_,l){var u,a,d,f,x,T,k,b,m,O,M,re,V,Le,oe,G,be,F=t.type;if(t.constructor!=null)return null;128&r.__u&&(_=!!(32&r.__u),n=[c=t.__e=r.__e]),(u=h.__b)&&u(t);e:if(typeof F=="function")try{if(b=t.props,m="prototype"in F&&F.prototype.render,O=(u=F.contextType)&&o[u.__c],M=u?O?O.props.value:u.__:o,r.__c?k=(a=t.__c=r.__c).__=a.__E:(m?t.__c=a=new F(b,M):(t.__c=a=new se(b,M),a.constructor=F,a.render=xr),O&&O.sub(a),a.state||(a.state={}),a.__n=o,d=a.__d=!0,a.__h=[],a._sb=[]),m&&a.__s==null&&(a.__s=a.state),m&&F.getDerivedStateFromProps!=null&&(a.__s==a.state&&(a.__s=P({},a.__s)),P(a.__s,F.getDerivedStateFromProps(b,a.__s))),f=a.props,x=a.state,a.__v=t,d)m&&F.getDerivedStateFromProps==null&&a.componentWillMount!=null&&a.componentWillMount(),m&&a.componentDidMount!=null&&a.__h.push(a.componentDidMount);else{if(m&&F.getDerivedStateFromProps==null&&b!==f&&a.componentWillReceiveProps!=null&&a.componentWillReceiveProps(b,M),t.__v==r.__v||!a.__e&&a.shouldComponentUpdate!=null&&a.shouldComponentUpdate(b,a.__s,M)===!1){for(t.__v!=r.__v&&(a.props=b,a.state=a.__s,a.__d=!1),t.__e=r.__e,t.__k=r.__k,t.__k.some(function(K){K&&(K.__=t)}),re=0;re<a._sb.length;re++)a.__h.push(a._sb[re]);a._sb=[],a.__h.length&&s.push(a);break e}a.componentWillUpdate!=null&&a.componentWillUpdate(b,a.__s,M),m&&a.componentDidUpdate!=null&&a.__h.push(function(){a.componentDidUpdate(f,x,T)})}if(a.context=M,a.props=b,a.__P=e,a.__e=!1,V=h.__r,Le=0,m){for(a.state=a.__s,a.__d=!1,V&&V(t),u=a.render(a.props,a.state,a.context),oe=0;oe<a._sb.length;oe++)a.__h.push(a._sb[oe]);a._sb=[]}else do a.__d=!1,V&&V(t),u=a.render(a.props,a.state,a.context),a.state=a.__s;while(a.__d&&++Le<25);a.state=a.__s,a.getChildContext!=null&&(o=P(P({},o),a.getChildContext())),m&&!d&&a.getSnapshotBeforeUpdate!=null&&(T=a.getSnapshotBeforeUpdate(f,x)),G=u,u!=null&&u.type===_e&&u.key==null&&(G=Ze(u.props.children)),c=qe(e,le(G)?G:[G],t,r,o,i,n,s,c,_,l),a.base=t.__e,t.__u&=-161,a.__h.length&&s.push(a),k&&(a.__E=a.__=null)}catch(K){if(t.__v=null,_||n!=null)if(K.then){for(t.__u|=_?160:128;c&&c.nodeType==8&&c.nextSibling;)c=c.nextSibling;n[n.indexOf(c)]=null,t.__e=c}else{for(be=n.length;be--;)we(n[be]);$e(t)}else t.__e=r.__e,t.__k=r.__k,K.then||$e(t);h.__e(K,t,r)}else n==null&&t.__v==r.__v?(t.__k=r.__k,t.__e=r.__e):c=t.__e=$r(r.__e,t,r,o,i,n,s,_,l);return(u=h.diffed)&&u(t),128&t.__u?void 0:c}function $e(e){e&&e.__c&&(e.__c.__e=!0),e&&e.__k&&e.__k.forEach($e)}function Je(e,t,r){for(var o=0;o<r.length;o++)Se(r[o],r[++o],r[++o]);h.__c&&h.__c(t,e),e.some(function(i){try{e=i.__h,i.__h=[],e.some(function(n){n.call(i)})}catch(n){h.__e(n,i.__v)}})}function Ze(e){return typeof e!="object"||e==null||e.__b&&e.__b>0?e:le(e)?e.map(Ze):P({},e)}function $r(e,t,r,o,i,n,s,c,_){var l,u,a,d,f,x,T,k=r.props||q,b=t.props,m=t.type;if(m=="svg"?i="http://www.w3.org/2000/svg":m=="math"?i="http://www.w3.org/1998/Math/MathML":i||(i="http://www.w3.org/1999/xhtml"),n!=null){for(l=0;l<n.length;l++)if((f=n[l])&&"setAttribute"in f==!!m&&(m?f.localName==m:f.nodeType==3)){e=f,n[l]=null;break}}if(e==null){if(m==null)return document.createTextNode(b);e=document.createElementNS(i,m,b.is&&b),c&&(h.__m&&h.__m(t,n),c=!1),n=null}if(m==null)k===b||c&&e.data==b||(e.data=b);else{if(n=n&&ce.call(e.childNodes),!c&&n!=null)for(k={},l=0;l<e.attributes.length;l++)k[(f=e.attributes[l]).name]=f.value;for(l in k)if(f=k[l],l!="children"){if(l=="dangerouslySetInnerHTML")a=f;else if(!(l in b)){if(l=="value"&&"defaultValue"in b||l=="checked"&&"defaultChecked"in b)continue;ne(e,l,null,f,i)}}for(l in b)f=b[l],l=="children"?d=f:l=="dangerouslySetInnerHTML"?u=f:l=="value"?x=f:l=="checked"?T=f:c&&typeof f!="function"||k[l]===f||ne(e,l,f,k[l],i);if(u)c||a&&(u.__html==a.__html||u.__html==e.innerHTML)||(e.innerHTML=u.__html),t.__k=[];else if(a&&(e.innerHTML=""),qe(t.type=="template"?e.content:e,le(d)?d:[d],t,r,o,m=="foreignObject"?"http://www.w3.org/1999/xhtml":i,n,s,n?n[0]:r.__k&&z(r,0),c,_),n!=null)for(l=n.length;l--;)we(n[l]);c||(l="value",m=="progress"&&x==null?e.removeAttribute("value"):x!=null&&(x!==e[l]||m=="progress"&&!x||m=="option"&&x!=k[l])&&ne(e,l,x,k[l],i),l="checked",T!=null&&T!=e[l]&&ne(e,l,T,k[l],i))}return e}function Se(e,t,r){try{if(typeof e=="function"){var o=typeof e.__u=="function";o&&e.__u(),o&&t==null||(e.__u=e(t))}else e.current=t}catch(i){h.__e(i,r)}}function Qe(e,t,r){var o,i;if(h.unmount&&h.unmount(e),(o=e.ref)&&(o.current&&o.current!=e.__e||Se(o,null,t)),(o=e.__c)!=null){if(o.componentWillUnmount)try{o.componentWillUnmount()}catch(n){h.__e(n,t)}o.base=o.__P=null}if(o=e.__k)for(i=0;i<o.length;i++)o[i]&&Qe(o[i],t,r||typeof e.type!="function");r||we(e.__e),e.__c=e.__=e.__e=void 0}function xr(e,t,r){return this.constructor(e,r)}function et(e,t,r){var o,i,n,s;t==document&&(t=document.documentElement),h.__&&h.__(e,t),i=(o=typeof r=="function")?null:r&&r.__k||t.__k,n=[],s=[],ke(t,e=(!o&&r||t).__k=p(_e,null,[e]),i||q,q,t.namespaceURI,!o&&r?[r]:i?null:t.firstChild?ce.call(t.childNodes):null,n,!o&&r?r:i?i.__e:t.firstChild,o,s),Je(n,e,s)}var ce,h,We,hr,U,Me,Ke,ze,Ye,xe,ge,ye,vr,q,Ve,br,le,E=g(()=>{q={},Ve=[],br=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,le=Array.isArray;ce=Ve.slice,h={__e:function(e,t,r,o){for(var i,n,s;t=t.__;)if((i=t.__c)&&!i.__)try{if((n=i.constructor)&&n.getDerivedStateFromError!=null&&(i.setState(n.getDerivedStateFromError(e)),s=i.__d),i.componentDidCatch!=null&&(i.componentDidCatch(e,o||{}),s=i.__d),s)return i.__E=i}catch(c){e=c}throw e}},We=0,hr=function(e){return e!=null&&e.constructor==null},se.prototype.setState=function(e,t){var r;r=this.__s!=null&&this.__s!=this.state?this.__s:this.__s=P({},this.state),typeof e=="function"&&(e=e(P({},r),this.props)),e&&P(r,e),e!=null&&this.__v&&(t&&this._sb.push(t),Ue(this))},se.prototype.forceUpdate=function(e){this.__v&&(this.__e=!0,e&&this.__h.push(e),Ue(this))},se.prototype.render=_e,U=[],Ke=typeof Promise=="function"?Promise.prototype.then.bind(Promise.resolve()):setTimeout,ze=function(e,t){return e.__v.__b-t.__v.__b},ae.__r=0,Ye=/(PointerCapture)$|Capture$/i,xe=0,ge=je(!1),ye=je(!0),vr=0});function _t(e,t){$.__h&&$.__h(y,e,De||t),De=0;var r=y.__H||(y.__H={__:[],__h:[]});return e>=r.__.length&&r.__.push({}),r.__[e]}function Te(e){return De=1,wr(ut,e)}function wr(e,t,r){var o=_t(de++,2);if(o.t=e,!o.__c&&(o.__=[r?r(t):ut(void 0,t),function(c){var _=o.__N?o.__N[0]:o.__[0],l=o.t(_,c);_!==l&&(o.__N=[l,o.__[1]],o.__c.setState({}))}],o.__c=y,!y.__f)){var i=function(c,_,l){if(!o.__c.__H)return!0;var u=o.__c.__H.__.filter(function(d){return!!d.__c});if(u.every(function(d){return!d.__N}))return!n||n.call(this,c,_,l);var a=o.__c.props!==c;return u.forEach(function(d){if(d.__N){var f=d.__[0];d.__=d.__N,d.__N=void 0,f!==d.__[0]&&(a=!0)}}),n&&n.call(this,c,_,l)||a};y.__f=!0;var n=y.shouldComponentUpdate,s=y.componentWillUpdate;y.componentWillUpdate=function(c,_,l){if(this.__e){var u=n;n=void 0,i(c,_,l),n=u}s&&s.call(this,c,_,l)},y.shouldComponentUpdate=i}return o.__N||o.__}function X(e,t){var r=_t(de++,3);!$.__s&&Er(r.__H,t)&&(r.__=e,r.u=t,y.__H.__h.push(r))}function kr(){for(var e;e=lt.shift();)if(e.__P&&e.__H)try{e.__H.__h.forEach(ue),e.__H.__h.forEach(Ce),e.__H.__h=[]}catch(t){e.__H.__h=[],$.__e(t,e.__v)}}function Sr(e){var t,r=function(){clearTimeout(o),ct&&cancelAnimationFrame(t),setTimeout(e)},o=setTimeout(r,35);ct&&(t=requestAnimationFrame(r))}function ue(e){var t=y,r=e.__c;typeof r=="function"&&(e.__c=void 0,r()),y=t}function Ce(e){var t=y;e.__c=e.__(),y=t}function Er(e,t){return!e||e.length!==t.length||t.some(function(r,o){return r!==e[o]})}function ut(e,t){return typeof t=="function"?t(e):t}var de,y,Ee,tt,De,lt,$,rt,ot,nt,it,st,at,ct,Ae=g(()=>{E();De=0,lt=[],$=h,rt=$.__b,ot=$.__r,nt=$.diffed,it=$.__c,st=$.unmount,at=$.__;$.__b=function(e){y=null,rt&&rt(e)},$.__=function(e,t){e&&t.__k&&t.__k.__m&&(e.__m=t.__k.__m),at&&at(e,t)},$.__r=function(e){ot&&ot(e),de=0;var t=(y=e.__c).__H;t&&(Ee===y?(t.__h=[],y.__h=[],t.__.forEach(function(r){r.__N&&(r.__=r.__N),r.u=r.__N=void 0})):(t.__h.forEach(ue),t.__h.forEach(Ce),t.__h=[],de=0)),Ee=y},$.diffed=function(e){nt&&nt(e);var t=e.__c;t&&t.__H&&(t.__H.__h.length&&(lt.push(t)!==1&&tt===$.requestAnimationFrame||((tt=$.requestAnimationFrame)||Sr)(kr)),t.__H.__.forEach(function(r){r.u&&(r.__H=r.u),r.u=void 0})),Ee=y=null},$.__c=function(e,t){t.some(function(r){try{r.__h.forEach(ue),r.__h=r.__h.filter(function(o){return!o.__||Ce(o)})}catch(o){t.some(function(i){i.__h&&(i.__h=[])}),t=[],$.__e(o,r.__v)}}),it&&it(e,t)},$.unmount=function(e){st&&st(e);var t,r=e.__c;r&&r.__H&&(r.__H.__.forEach(function(o){try{ue(o)}catch(i){t=i}}),r.__H=void 0,t&&$.__e(t,r.__v))};ct=typeof requestAnimationFrame=="function"});var A,R,fe,Dr,N,Fe=g(()=>{A=[],R=0,fe=4,Dr=0,N=e=>{let t=[],r={get(){return r.lc||r.listen(()=>{})(),r.value},lc:0,listen(o){return r.lc=t.push(o),()=>{for(let n=R+fe;n<A.length;)A[n]===o?A.splice(n,fe):n+=fe;let i=t.indexOf(o);~i&&(t.splice(i,1),--r.lc||r.off())}},notify(o,i){Dr++;let n=!A.length;for(let s of t)A.push(s,r.value,o,i);if(n){for(R=0;R<A.length;R+=fe)A[R](A[R+1],A[R+2],A[R+3]);A.length=0}},off(){},set(o){let i=r.value;i!==o&&(r.value=o,r.notify(i))},subscribe(o){let i=r.listen(o);return o(r.value),i},value:e};return r}});function Ne(e,t,r){let o=new Set(t).add(void 0);return e.listen((i,n,s)=>{o.has(s)&&r(i,n,s)})}var dt=g(()=>{});var J,ft=g(()=>{Fe();J=(e={})=>{let t=N(e);return t.setKey=function(r,o){let i=t.value;typeof o>"u"&&r in t.value?(t.value={...t.value},delete t.value[r],t.notify(i,r)):t.value[r]!==o&&(t.value={...t.value,[r]:o},t.notify(i,r))},t}});var Pe=g(()=>{Fe();dt();ft()});function w(e,t={}){let[,r]=Te({}),[o]=Te(e.get());return X(()=>{o!==e.get()&&r({})},[]),X(()=>{let i,n,s,c=()=>{i||(i=1,n=setTimeout(()=>{i=void 0,r({})}))};return t.keys?s=Ne(e,t.keys,c):s=e.listen(c),()=>{s(),clearTimeout(n)}},[e,""+t.keys]),e.get()}var B=g(()=>{Pe();Ae()});function v(e){var t=pt.get(this);return t||(t=new Map,pt.set(this,t)),(t=mt(this,t.get(e)||(t.set(e,t=(function(r){for(var o,i,n=1,s="",c="",_=[0],l=function(d){n===1&&(d||(s=s.replace(/^\s*\n\s*|\s*\n\s*$/g,"")))?_.push(0,d,s):n===3&&(d||s)?(_.push(3,d,s),n=2):n===2&&s==="..."&&d?_.push(4,d,0):n===2&&s&&!d?_.push(5,0,!0,s):n>=5&&((s||!d&&n===5)&&(_.push(n,0,s,i),n=6),d&&(_.push(n,d,0,i),n=6)),s=""},u=0;u<r.length;u++){u&&(n===1&&l(),l(u));for(var a=0;a<r[u].length;a++)o=r[u][a],n===1?o==="<"?(l(),_=[_],n=3):s+=o:n===4?s==="--"&&o===">"?(n=1,s=""):s=o+s[0]:c?o===c?c="":s+=o:o==='"'||o==="'"?c=o:o===">"?(l(),n=1):n&&(o==="="?(n=5,i=s,s=""):o==="/"&&(n<5||r[u][a+1]===">")?(l(),n===3&&(_=_[0]),n=_,(_=_[0]).push(2,0,n),n=0):o===" "||o==="	"||o===`
`||o==="\r"?(l(),n=2):s+=o),n===3&&s==="!--"&&(n=4,_=_[0])}return l(),_})(e)),t),arguments,[])).length>1?t:t[0]}var mt,pt,D=g(()=>{mt=function(e,t,r,o){var i;t[0]=0;for(var n=1;n<t.length;n++){var s=t[n++],c=t[n]?(t[0]|=s?1:2,r[t[n++]]):t[++n];s===3?o[0]=c:s===4?o[1]=Object.assign(o[1]||{},c):s===5?(o[1]=o[1]||{})[t[++n]]=c:s===6?o[1][t[++n]]+=c+"":s?(i=e.apply(c,mt(e,c,r,["",null])),o.push(i),c[0]?t[0]|=2:(t[n-2]=0,t[n]=i)):o.push(c)}return o},pt=new Map});var Cr,Tr,ht,Ar,vt,bt,gt,yt,$t,xt=g(()=>{Cr="/api/services",Tr="/api/especialistas/disponibles",ht="/api/reservas",Ar="/api/me",vt=async()=>{try{let e=await fetch(Cr);if(!e.ok)throw new Error("Error al obtener servicios");return await e.json()}catch(e){return console.error("Error al cargar servicios:",e),[]}},bt=async(e,t,r=null,o=null)=>{if(!e)return{data:[],total:0};if(!t)return{data:[],total:0};try{let i=`${Tr}?servicio=${e}&fecha=${t}`;r!==null&&(i+=`&limit=${r}`),o!==null&&(i+=`&offset=${o}`);let n=await fetch(i);if(!n.ok)throw new Error("Error al obtener especialistas disponibles");return await n.json()}catch(i){return console.error("Error al cargar especialistas:",i),{data:[],total:0}}},gt=async()=>{try{let e=await fetch(ht);if(!e.ok)throw new Error("Error al obtener reservas");return(await e.json()).reservas||[]}catch(e){return console.error("Error al cargar reservas:",e),[]}},yt=async e=>{if(!e||typeof e!="object")throw new Error("Los datos de la reserva son obligatorios");let{servicio_id:t,especialista_id:r,fecha:o,hora:i}=e;if(!t)throw new Error("Debes seleccionar un servicio");if(!r)throw new Error("Debes seleccionar un especialista");if(!o)throw new Error("Debes seleccionar una fecha");if(!i)throw new Error("Debes seleccionar una hora");try{let n=await fetch(ht,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(e)});if(!n.ok){let s=await n.json();throw new Error(s.error||"Error al crear la reserva")}return await n.json()}catch(n){throw console.error("Error al crear reserva:",n),n}},$t=async()=>{try{let e=await fetch(Ar);if(!e.ok)return null;let t=await e.json();return t.success?t.data:null}catch(e){return console.error("Error al obtener usuario:",e),null}}});var wt,Ie,Fr,kt,pe=g(()=>{wt=(e,t=!0)=>{let r=e.toLocaleDateString("es-ES",{weekday:"long",day:"numeric",month:"long",year:"numeric"});return t?r.charAt(0).toUpperCase()+r.slice(1):r},Ie=e=>{let t=e.getFullYear(),r=String(e.getMonth()+1).padStart(2,"0"),o=String(e.getDate()).padStart(2,"0");return`${t}-${r}-${o}`},Fr=e=>{let t=new Date;return e.getDate()===t.getDate()&&e.getMonth()===t.getMonth()&&e.getFullYear()===t.getFullYear()},kt=(e,t)=>{if(!Fr(e))return!1;let[r,o]=t.split(":").map(Number),i=new Date;return i.setHours(r,o,0,0),i<=new Date}});var St,Et,Dt,Nr,Ct,Tt=g(()=>{St=e=>{let t=new Date(e),r=t.getDay(),o=t.getDate()-r+(r===0?-6:1);return new Date(t.setDate(o))},Et=(e,t)=>{let r=St(e),o=St(t);return r.getTime()===o.getTime()},Dt=(e,t,r)=>e.filter(i=>i.estado!=="Cancelada").some(i=>{let n=i.id_servicio===t,s=Et(new Date(i.fecha_reserva),new Date(r));return n&&s}),Nr=(e,t)=>{let r=e.filter(n=>n.estado!=="Cancelada"),o=new Date(t);return r.reduce((n,s)=>Et(new Date(s.fecha_reserva),o)?n+(s.duracion_minutos||60):n,0)/60},Ct=(e,t,r,o=40)=>Nr(e,t)+r/60>o});var C,Oe,me,Re,S,j,H,Z,At,Pr,Ft,Nt,Pt,It,He,Ot,Rt,Ht,Ir,W=g(()=>{Pe();xt();pe();Tt();C=N("ServiceForm"),Oe=N([]),me=N([]),Re=N("Usuario"),S=J({serviceId:null,service:null,dia:new Date,especialista:null,hora:null}),j=J({current:1,pageSize:2,totalPages:0}),H=J({loading:!1,error:null}),Z=N(new Date),At=N(0),Pr=async()=>{let e=await $t();e?.nombre&&Re.set(e.nombre)},Ft=async()=>{Pr();let e=await vt();console.log("Servicios cargados:",e),Oe.set(e.servicios||[])},Nt=async e=>{S.setKey("service",e),S.setKey("serviceId",e.id),await He(),C.set("DateForm")},Pt=e=>{Z.set(e)},It=async e=>{S.setKey("dia",e),Z.set(e),j.setKey("current",1),await He()},He=async(e=null)=>{let t=S.get(),r=j.get(),o=e??r.current;if(!t.service){console.log("No hay servicio seleccionado");return}if(!t.service.id){console.warn("El servicio seleccionado no tiene ID");return}if(!t.dia){console.warn("No hay fecha seleccionada");return}let i=Ie(t.dia),n=(o-1)*r.pageSize,s=await bt(t.service.id,i,r.pageSize,n);me.set(s.data||[]),At.set(s.total||0);let c=Math.ceil((s.total||0)/r.pageSize);j.setKey("current",o),j.setKey("totalPages",c)},Ot=(e,t)=>{S.setKey("especialista",e),S.setKey("hora",t),console.log("Selecci\xF3n guardada:",{especialista:e,hora:t})},Rt=async e=>{await He(e)},Ht=async()=>{let e=S.get();if(!e.service?.id||!e.especialista?.id_especialista||!e.dia||!e.hora){H.setKey("error","Faltan datos requeridos para completar la reserva");return}H.setKey("loading",!0),H.setKey("error",null);let t=!1;try{let r=await gt(),o=Ie(e.dia);if(Dt(r,e.service.id,o))throw new Error("Ya tienes una reserva de este servicio en esta semana");if(Ct(r,o,e.service.duracion_minutos||60))throw new Error("Ya has alcanzado el m\xE1ximo de 40 horas permitidas por ley para esta semana");let i={servicio_id:e.service.id,especialista_id:e.especialista.id_especialista,fecha:o,hora:e.hora,duracion:e.service.duracion_minutos};await yt(i),t=!0}catch(r){H.setKey("error",r.message)}finally{H.setKey("loading",!1)}t&&setTimeout(()=>{Ir(),globalThis.location.href="/user/reservas"},800)},Ir=()=>{C.set("ServiceForm"),S.set({serviceId:null,service:null,dia:new Date,especialista:null,hora:null}),Z.set(new Date),me.set([]),At.set(0),j.set({current:1,pageSize:2,totalPages:0}),H.set({loading:!1,error:null})}});var I,Or,Rr,he,ve,Hr,Lt,Lr,Mr,Ur,Br,jr,Wr,Kr,Mt=g(()=>{E();D();I=v.bind(p),Or=["L","M","X","J","V","S","D"],Rr="380px",he="40px",ve="40px",Hr=7,Lt=({fecha:e,diaSeleccionado:t,handleDiaChange:r,handleMesChange:o})=>{if(!e||!r||!o)return null;let i=e.getFullYear(),n=e.getMonth(),s=e.toLocaleString("es-ES",{month:"long"}),c=i,_=new Date(i,n+1,0).getDate(),l=(new Date(i,n,1).getDay()||Hr)-1,u=Lr(l,_);return I`
    <div
      class="card border-0 shadow-sm rounded-4 p-4 bg-white"
      style="width: ${Rr}; margin: 0 auto;"
    >
      ${Mr(i,n,s,c,o)}
      <div class="row g-0 text-center small text-primary fw-bold mb-2">
        ${Or.map(a=>I`
              <div class="col">${a}</div>
            `)}
      </div>
      ${Br(u,i,n,t,r)}
    </div>
  `},Lr=(e,t)=>{let r=[];for(let o=0;o<e;o++)r.push(null);for(let o=1;o<=t;o++)r.push(o);for(;r.length%7!==0;)r.push(null);return r},Mr=(e,t,r,o,i)=>{let s=Ur(e,t,new Date);return I`
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
      <button
        class="btn btn-sm btn-link text-decoration-none text-dark p-0"
        disabled=${s}
        onClick=${()=>i(new Date(e,t-1))}
        aria-label="Mes anterior"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="fw-bold text-capitalize fs-5">${r} ${o}</div>
      <button
        class="btn btn-sm btn-link text-decoration-none text-dark p-0"
        onClick=${()=>i(new Date(e,t+1))}
        aria-label="Mes siguiente"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  `},Ur=(e,t,r)=>e<r.getFullYear()||e===r.getFullYear()&&t<=r.getMonth(),Br=(e,t,r,o,i)=>{let n=[];for(let s=0;s<e.length;s+=7)n.push(e.slice(s,s+7));return n.map(s=>I`
      <div class="row g-0 text-center mb-2">
        ${s.map(c=>I`
              <div class="col d-flex justify-content-center">
                ${jr(c,t,r,o,i)}
              </div>
            `)}
      </div>
    `)},jr=(e,t,r,o,i)=>{if(!e)return I`
      <div style="width: ${he}; height: ${ve};"></div>
    `;let n=Wr(e,r,t,o),s=Kr(e,r,t),c=new Date(t,r,e).getDay()===0;return s||c?I`
      <span
        class="d-flex align-items-center justify-content-center text-secondary"
        style="width: ${he}; height: ${ve};"
        aria-label="Día ${e} no disponible"
      >
        ${e}
      </span>
    `:n?I`
      <button
        type="button"
        class="btn rounded-circle d-flex align-items-center justify-content-center bg-primary text-white border-0"
        style="width: ${he}; height: ${ve};"
        onClick=${()=>i(new Date(t,r,e))}
        aria-label="Día ${e}"
      >
        ${e}
      </button>
    `:I`
    <button
      type="button"
      class="btn rounded-circle d-flex align-items-center justify-content-center text-black border-0"
      style="width: ${he}; height: ${ve};"
      onClick=${()=>i(new Date(t,r,e))}
      aria-label="Día ${e}"
    >
      ${e}
    </button>
  `},Wr=(e,t,r,o)=>o?e===o.getDate()&&t===o.getMonth()&&r===o.getFullYear():!1,Kr=(e,t,r)=>{let o=new Date(r,t,e),i=new Date;return i.setHours(0,0,0,0),o<i}});var Ut,zr,Yr,Vr,Bt,jt=g(()=>{E();D();Ut=v.bind(p),zr=(e,t)=>{e>1&&t(e-1)},Yr=(e,t,r)=>{e<t&&r(e+1)},Vr=(e,t,r)=>{let o=[];for(let i=1;i<=e;i++)o.push(Ut`
        <li class="page-item ${t===i?"active":""}">
          <button class="page-link" onClick=${()=>r(i)} disabled=${t===i}>
            ${i}
          </button>
        </li>
      `);return o},Bt=({currentPage:e,totalPages:t,onPageChange:r})=>t<=1?null:Ut`
    <nav aria-label="Pagination">
      <ul class="pagination justify-content-center">
        <li class="page-item ${e===1?"disabled":""}">
          <button
            class="page-link"
            onClick=${()=>zr(e,r)}
            disabled=${e===1}
            aria-label="Previous"
          >
            <span aria-hidden="true">«</span>
            <span class="visually-hidden">Anterior</span>
          </button>
        </li>

        ${Vr(t,e,r)}

        <li class="page-item ${e===t?"disabled":""}">
          <button
            class="page-link"
            onClick=${()=>Yr(e,t,r)}
            disabled=${e===t}
            aria-label="Next"
          >
            <span aria-hidden="true">»</span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </li>
      </ul>
    </nav>
  `});var L,Wt,Gr,qr,Xr,Jr,Zr,Qr,eo,Kt,zt=g(()=>{E();D();jt();pe();L=v.bind(p),Wt="150px",Gr="cover",qr="top center",Xr=()=>L`
    <div class="text-center py-5">
      <i class="bi bi-calendar-x fs-1 text-muted"></i>
      <p class="text-muted mt-2">No hay especialistas disponibles para esta fecha</p>
    </div>
  `,Jr=(e,t)=>e?L`
      <img
        src="${e}"
        alt="${t}"
        class="img-fluid w-100 h-100"
        style="object-fit: ${Gr}; object-position: ${qr}; min-height: ${Wt};"
      />
    `:L`
    <div
      class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-secondary"
      style="min-height: ${Wt};"
    >
      <i class="bi bi-person-fill fs-1"></i>
    </div>
  `,Zr=(e,t,r,o,i)=>L`
    <button
      class="${t?"btn btn-primary btn-sm px-3":r?"btn btn-outline-secondary btn-sm px-3 text-muted":"btn btn-outline-primary btn-sm px-3"}"
      onClick=${()=>!r&&o(i,e)}
      disabled=${r}
      title=${r?"Esta hora ya ha pasado":""}
      style="${r?"cursor: not-allowed; opacity: 0.5;":""}"
      aria-label="Seleccionar hora ${e}"
    >
      ${e}
      ${r?L`
            <i class="bi bi-lock-fill ms-1 small"></i>
          `:""}
    </button>
  `,Qr=(e,t,r,o,i,n)=>L`
    <div>
      <strong class="d-block small text-secondary mb-2">Horarios disponibles:</strong>
      <div class="d-flex flex-wrap gap-2">
        ${e.map(s=>{let c=t&&r&&t.id_especialista===n.id_especialista&&r===s,_=o&&kt(o,s);return Zr(s,c,_,i,n)})}
      </div>
    </div>
  `,eo=(e,t,r,o,i)=>L`
    <div class="card border border-0 shadow-sm overflow-hidden">
      <div class="row g-0">
        <div class="col-4 col-sm-3 col-md-2 p-0 position-relative">
          ${Jr(e.foto_url,e.nombre)}
        </div>
        <div class="col-8 col-sm-9 col-md-10">
          <div class="card-body">
            <h5 class="card-title fw-bold">${e.nombre} ${e.apellidos}</h5>
            <p class="card-text text-muted small mb-3">${e.descripcion}</p>
            ${Qr(e.horas_disponibles,t,r,o,i,e)}
          </div>
        </div>
      </div>
    </div>
  `,Kt=({especialistas:e,onSelectHora:t,selectedEspecialista:r,selectedHora:o,currentPage:i,totalPages:n,onPageChange:s,diaSeleccionado:c})=>e.length===0?Xr():L`
    <div>
      <h5 class="card-title mb-3">Especialistas disponibles</h5>
      <div class="d-flex flex-column gap-3 mb-4">
        ${e.map(_=>eo(_,r,o,c,t))}
      </div>

      <${Bt}
        currentPage=${i}
        totalPages=${n}
        onPageChange=${s}
      />
    </div>
  `});var to,Yt,Vt=g(()=>{E();B();D();Mt();zt();W();to=v.bind(p),Yt=()=>{let e=w(S),t=w(me),r=w(j),o=w(Z);return to`
    <div class="row g-4">
      <div class="col-12 col-lg-5">
        <div>
          <div>
            <div class="d-flex justify-content-center">
              <${Lt}
                fecha=${o}
                diaSeleccionado=${e.dia}
                handleMesChange=${Pt}
                handleDiaChange=${It}
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-7">
        <div>
          <div>
            <${Kt}
              especialistas=${t}
              onSelectHora=${Ot}
              selectedEspecialista=${e.especialista}
              selectedHora=${e.hora}
              currentPage=${r.current}
              totalPages=${r.totalPages}
              onPageChange=${Rt}
              diaSeleccionado=${e.dia}
            />
          </div>
        </div>
      </div>
    </div>
  `}});var ro,oo,no,io,Gt,qt=g(()=>{E();D();ro=v.bind(p),oo="120px",no="rgba(13, 110, 253, 0.5)",io=(e,t)=>{t&&t(e)},Gt=({service:e,onSelect:t,isSelected:r=!1})=>ro`
    <div
      class="card ${r?"border-primary border-3":""}"
      onclick=${()=>io(e,t)}
      style="cursor: pointer; min-height: ${oo}; ${r?`box-shadow: 0 0 10px ${no};`:""}"
    >
      <div class="card-body d-flex align-items-center gap-3">
        <i class="bi bi-scissors fs-1 text-primary"></i>
        <div class="flex-grow-1">
          <h5 class="card-title mb-2">${e.nombre_servicio}</h5>
          <p class="card-text text-muted mb-0">
            <i class="bi bi-clock me-1"></i>
            ${e.duracion_minutos} minutos
          </p>
        </div>
      </div>
    </div>
  `});var Xt,Jt,Zt=g(()=>{E();B();D();qt();W();Xt=v.bind(p),Jt=()=>{let e=w(Oe),t=w(S);return Xt`
    <div class="container-fluid p-3 d-flex flex-column align-items-center">
      <div class="row g-3">
        ${e.map(r=>Xt`
              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <${Gt}
                  service=${r}
                  onSelect=${Nt}
                  isSelected=${t.serviceId===r.id}
                />
              </div>
            `)}
      </div>
    </div>
  `}});var Qt,er=g(()=>{B();W();Qt=()=>{let e=w(S),t=w(H);return{selectedService:e.service,dia:e.dia,selectedEspecialista:e.especialista,selectedHora:e.hora,loading:t.loading,error:t.error,confirmarReserva:Ht}}});var so,tr,ao,co,lo,_o,Q,rr=g(()=>{E();D();so=v.bind(p),tr="48px",ao="#fce7f3",co="#e83e8c",lo="24px",_o="#2d3748",Q=({icon:e,label:t,value:r})=>so`
    <div class="d-flex gap-3 mb-4">
      <div
        class="d-flex align-items-center justify-content-center rounded-3"
        style="width: ${tr}; height: ${tr}; background-color: ${ao}; flex-shrink: 0;"
      >
        <i class="bi bi-${e}" style="font-size: ${lo}; color: ${co};"></i>
      </div>
      <div>
        <p class="text-muted small mb-1">${t}</p>
        <p class="fw-semibold mb-0" style="color: ${_o};">${r}</p>
      </div>
    </div>
  `});var uo,fo,po,or,nr=g(()=>{E();D();rr();pe();uo=v.bind(p),fo="#f8f9fa",po="#2d3748",or=({selectedService:e,selectedEspecialista:t,dia:r,selectedHora:o})=>{let i=wt(r);return uo`
    <div
      class="card border-0 shadow-sm rounded-4 p-4"
      style="background-color: ${fo};"
    >
      <h5 class="fw-bold mb-4" style="color: ${po};">Resumen de la Cita</h5>

      <${Q}
        icon="scissors"
        label="Servicio"
        value=${e?.nombre||e?.nombre_servicio}
      />

      <${Q}
        icon="person"
        label="Especialista"
        value="${t?.nombre} ${t?.apellidos}"
      />

      <${Q}
        icon="calendar-event"
        label="Fecha y Hora"
        value="${i} a las ${o||"..."}"
      />

      <div class="d-flex gap-3">
        <${Q}
          icon="clock"
          label="Duración"
          value="Aprox. ${e?.duracion_minutos||0} min"
        />
      </div>
    </div>
  `}});var ir,mo,ho,sr,ar=g(()=>{E();D();ir=v.bind(p),mo=()=>ir`
  <div class="alert alert-info d-flex align-items-center" role="alert">
    <div class="spinner-border spinner-border-sm me-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div>Confirming your booking...</div>
  </div>
`,ho=e=>ir`
  <div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    ${e}
  </div>
`,sr=({loading:e,error:t})=>e?mo():t?ho(t):null});var cr,vo,bo,go,yo,$o,lr,_r=g(()=>{E();D();cr=v.bind(p),vo="px-4",bo="px-5",go="#cbd5e0",yo="#4a5568",$o="#e83e8c",lr=({loading:e,onModificar:t,onConfirmar:r})=>cr`
    <div class="d-flex gap-3 justify-content-end flex-wrap">
      <button
        class="btn btn-outline-secondary rounded-pill ${vo}"
        onclick=${t}
        disabled=${e}
        style="border-color: ${go}; color: ${yo};"
      >
        Modificar selección
      </button>
      <button
        class="btn rounded-pill ${bo}"
        onclick=${r}
        disabled=${e}
        style="background-color: ${$o}; border: none; color: white; font-weight: 600;"
      >
        ${e?cr`
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              Confirmando...
            `:"Confirmar Reserva"}
      </button>
    </div>
  `});var xo,ur,dr=g(()=>{E();D();B();W();er();nr();ar();_r();xo=v.bind(p),ur=()=>{let e=w(Re),{selectedService:t,dia:r,selectedEspecialista:o,selectedHora:i,loading:n,error:s,confirmarReserva:c}=Qt();return xo`
    <div class="container py-4">
      <div class="mb-4">
        <h2 class="fw-bold mb-2" style="color: #2d3748;">Confirma tu Cita</h2>
        <p class="text-muted">Revisa los detalles de tu reserva antes de confirmar.</p>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-5">
          <${or}
            selectedService=${t}
            selectedEspecialista=${o}
            dia=${r}
            selectedHora=${i}
          />
        </div>

        <div class="col-12 col-lg-7">
          <div
            class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-between"
          >
            <div>
              <h5 class="fw-bold mb-3" style="color: #2d3748;">Todo listo, ${e}</h5>
              <p class="text-muted mb-4">
                Solo falta un paso para confirmar tu cita. Tus datos se rellenarán automáticamente.
              </p>

              <${sr} loading=${n} error=${s} />
            </div>

            <${lr}
              loading=${n}
              onModificar=${()=>{C.set("DateForm")}}
              onConfirmar=${c}
            />
          </div>
        </div>
      </div>
    </div>
  `}});var te,ee,wo,ko,So,Eo,Do,Co,To,Ao,Fo,fr,pr=g(()=>{E();D();B();W();te=v.bind(p),ee="40px",wo="120px",ko=1e3,So=e=>{switch(e){case"ServiceForm":return{number:1,title:"Servicios"};case"DateForm":return{number:2,title:"Fecha y Especialista"};case"ConfirmationForm":return{number:3,title:"Confirmaci\xF3n"};default:return{number:0,title:""}}},Eo=e=>{e==="DateForm"&&C.set("ServiceForm"),e==="ConfirmationForm"&&C.set("DateForm")},Do=e=>{e==="ServiceForm"&&C.set("DateForm"),e==="DateForm"&&C.set("ConfirmationForm")},Co=(e,t)=>e==="ServiceForm"&&t.service||e==="DateForm"&&t.especialista&&t.hora,To=(e,t)=>te`
  <button
    class="btn btn-light rounded-circle text-secondary"
    style="width: ${ee}; height: ${ee};"
    onClick=${t}
    disabled=${!e}
    aria-label="Paso anterior"
  >
    <i class="bi bi-chevron-left"></i>
  </button>
`,Ao=e=>te`
  <div class="text-center" style="min-width: ${wo};">
    <div
      class="text-xs text-muted fw-bold text-uppercase"
      style="font-size: 0.7rem; letter-spacing: 1px;"
    >
      Paso ${e.number}/3
    </div>
    <div class="fw-bold fs-6">${e.title}</div>
  </div>
`,Fo=(e,t,r)=>e!=="ConfirmationForm"?te`
      <button
        class="btn btn-primary rounded-circle"
        style="width: ${ee}; height: ${ee};"
        onClick=${r}
        disabled=${!t}
        aria-label="Siguiente paso"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    `:te`
    <div style="width: ${ee};"></div>
  `,fr=()=>{let e=w(C),t=w(S),r=e!=="ServiceForm",o=Co(e,t),i=So(e);return te`
    <div
      class="position-sticky bottom-0 pb-4 d-flex justify-content-center"
      style="z-index: ${ko}; pointer-events: none;"
    >
      <div
        class="bg-white shadow-lg rounded-pill px-4 py-2 d-flex align-items-center gap-4 border"
        style="pointer-events: auto;"
      >
        ${To(r,()=>Eo(e))} ${Ao(i)}
        ${Fo(e,o,()=>Do(e))}
      </div>
    </div>
  `}});var Io=mr(()=>{E();Ae();B();D();W();Vt();Zt();dr();pr();var Y=v.bind(p),No=()=>{let e=w(C);return X(()=>{Ft()},[]),Y`
    <div class="d-flex flex-column position-relative" style="min-height: 75vh;">
      <h1 class="h2 text-dark mb-5">Nueva Reserva</h1>

      <!-- Content Area -->
      <div class="flex-grow-1 mb-5">${Po(e)}</div>

      <${fr} />
    </div>
  `},Po=e=>e?e==="ConfirmationForm"?Y`
      <${ur} />
    `:e==="DateForm"?Y`
      <${Yt} />
    `:Y`
    <${Jt} />
  `:Y`
      <div class="alert alert-warning">Cargando...</div>
    `;et(Y`
    <${No} />
  `,document.getElementById("bookings-app"))});export default Io();
//# sourceMappingURL=bookingsApp.js.map
