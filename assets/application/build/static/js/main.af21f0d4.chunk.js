(this["webpackJsonpadvanced-database-replacer"]=this["webpackJsonpadvanced-database-replacer"]||[]).push([[1],{130:function(e,t,n){"use strict";var c=n(10),r=(n(0),n(351)),a=n(36);t.a=function(){return Object(c.jsx)(r.a,{count:Object(a.a)("coming_soon"),style:{backgroundColor:"#52c41a",marginLeft:10}})}},350:function(e,t,n){"use strict";n.r(t);var c=n(10),r=n(0),a=n.n(r),i=n(18),s=n.n(i),o=n(57),l=n(153),j=n.n(l),b=n(355),u=n(359),d=n(352),O=n(356),h=n(361),f=n(362),p=n(363),x=n(364),v=n(360),m=n(36),w=n(130),g=n(358),y=n(75),k=n(176),S=function(e){var t=e.modalCb;return Object(c.jsx)(g.a,{message:Object(m.a)("alert.message"),description:Object(c.jsx)(k.a,{mask:!0,interval:5e3,children:Object.keys(Object(m.a)("alert.banner")).map((function(e){return Object(c.jsx)("div",{children:Object(m.a)("alert.banner.".concat(e))},"banner-".concat(e))}))}),type:"warning",showIcon:!0,action:Object(c.jsx)(y.a,{icon:Object(c.jsx)(v.a,{}),onClick:function(){return t(!0)},children:Object(m.a)("alert.button")})})},C=n(357),I=n(353),z=function(e){var t=e.visible,n=e.visibleCb,a=Object(r.useState)(!1),i=Object(o.a)(a,2),s=i[0],l=i[1];return Object(c.jsx)(C.a,{visible:t,onCancel:function(){return n(!1)},footer:null,children:Object(c.jsx)(I.a,{spinning:!s,children:Object(c.jsx)("iframe",{src:t?window.adr.checkoutUrl:void 0,title:"checkout",style:{width:"100%",border:"none",height:"80vh"},onLoad:function(){return l(!0)}})})})},P=(n(234),a.a.lazy((function(){return Promise.all([n.e(0),n.e(5),n.e(7)]).then(n.bind(null,734))}))),E=a.a.lazy((function(){return n.e(8).then(n.bind(null,733))})),F=a.a.lazy((function(){return Promise.all([n.e(0),n.e(4),n.e(6)]).then(n.bind(null,730))})),L=function(){var e=new URL(window.location.href),t=Object(r.useState)(e.searchParams.get("tab")||"replacer"),n=Object(o.a)(t,2),a=n[0],i=n[1],s=Object(r.useState)(!1),l=Object(o.a)(s,2),g=l[0],y=l[1],k=b.a.useForm(),C=Object(o.a)(k,1)[0],I=Object(r.useState)(0),L=Object(o.a)(I,2),J=L[0],K=L[1];return Object(c.jsxs)("div",{style:{padding:"15px 15px 0 0"},children:[Object(c.jsx)(j.a,{level:2,children:Object(m.a)("title")}),Object(c.jsxs)(u.b,{direction:"vertical",size:"middle",style:{width:"100%"},children:[!Object(m.c)()&&Object(c.jsx)(S,{modalCb:y}),Object(c.jsxs)(d.a,{mode:"horizontal",selectedKeys:[a],onClick:function(t){var n;"pro"!==(n=t.key)?(e.searchParams.set("tab",String(n)),window.history.pushState({},"",e.toString()),i(String(n))):y(!0)},children:[Object(c.jsx)(d.a.Item,{icon:Object(c.jsx)(h.a,{}),children:Object(m.a)("menu.replacer")},"replacer"),Object(c.jsxs)(d.a.Item,{disabled:!0,icon:Object(c.jsx)(f.a,{}),children:[Object(m.a)("menu.templates"),Object(c.jsx)(w.a,{})]},"templates"),Object(c.jsx)(d.a.Item,{icon:Object(c.jsx)(p.a,{}),children:Object(m.a)("menu.help")},"help"),Object(c.jsx)(d.a.Item,{icon:Object(c.jsx)(x.a,{}),children:Object(m.a)("menu.history")},"history"),!Object(m.c)()&&Object(c.jsx)(d.a.Item,{icon:Object(c.jsx)(v.a,{}),children:Object(m.a)("menu.pro")},"pro")]}),Object(c.jsxs)("div",{style:{width:"100%",backgroundColor:"white",padding:20},children:["replacer"===a&&Object(c.jsx)(r.Suspense,{fallback:Object(c.jsx)(O.a,{active:!0}),children:Object(c.jsx)(P,{form:C,tab:J,setTab:K})}),"help"===a&&Object(c.jsx)(r.Suspense,{fallback:Object(c.jsx)(O.a,{active:!0}),children:Object(c.jsx)(E,{})}),"history"===a&&Object(c.jsx)(r.Suspense,{fallback:Object(c.jsx)(O.a,{active:!0}),children:Object(c.jsx)(F,{})})]})]}),!Object(m.c)()&&Object(c.jsx)(z,{visible:g,visibleCb:y})]})};s.a.render(Object(c.jsx)(L,{}),document.getElementById("adr-application-wrapper"))},36:function(e,t,n){"use strict";n.d(t,"c",(function(){return c})),n.d(t,"b",(function(){return r})),n.d(t,"a",(function(){return i}));var c=function(){var e;return"1"===(null!==(e=window.adr.isPro)&&void 0!==e?e:"0")},r=function(e){var t,n;if(!(null===(t=window)||void 0===t||null===(n=t.adr)||void 0===n?void 0:n.form))return null;var c=window.adr.form,r=c.filter((function(e){return null===e.parent}));return r.map((function(e){return e.subFields=c.filter((function(t){return t.parent===e.id})).map((function(e){return e.subFields=c.filter((function(t){return t.parent===e.id})),e})),e})),r[e]?r[e]:null},a=window.adr.translation,i=function(e){var t,n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";if(!(null===(t=window.adr)||void 0===t?void 0:t.translation))return n;var c=e.replace(/\[(\w+)\]/g,".$1").replace(/^\./,""),r=a;return c.split(".").forEach((function(e){if("string"===typeof r&&!n)throw Error("Key ".concat(c," does not exists in translation"));r="object"===typeof r&&e in r?r[e]:n})),r.length?r:n}}},[[350,2,3]]]);
//# sourceMappingURL=main.af21f0d4.chunk.js.map