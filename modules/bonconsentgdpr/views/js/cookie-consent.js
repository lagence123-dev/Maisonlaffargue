var cookieconsent = (function (e) {
  var t = {};
  function i(n) {
    if (t[n]) return t[n].exports;
    var o = (t[n] = { i: n, l: !1, exports: {} });
    return e[n].call(o.exports, o, o.exports, i), (o.l = !0), o.exports;
  }
  return (
    (i.m = e),
    (i.c = t),
    (i.d = function (e, t, n) {
      i.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: n });
    }),
    (i.r = function (e) {
      'undefined' != typeof Symbol &&
        Symbol.toStringTag &&
        Object.defineProperty(e, Symbol.toStringTag, { value: 'Module' }),
        Object.defineProperty(e, '__esModule', { value: !0 });
    }),
    (i.t = function (e, t) {
      if ((1 & t && (e = i(e)), 8 & t)) return e;
      if (4 & t && 'object' == typeof e && e && e.__esModule) return e;
      var n = Object.create(null);
      if (
        (i.r(n),
        Object.defineProperty(n, 'default', { enumerable: !0, value: e }),
        2 & t && 'string' != typeof e)
      )
        for (var o in e)
          i.d(
            n,
            o,
            function (t) {
              return e[t];
            }.bind(null, o)
          );
      return n;
    }),
    (i.n = function (e) {
      var t =
        e && e.__esModule
          ? function () {
              return e.default;
            }
          : function () {
              return e;
            };
      return i.d(t, 'a', t), t;
    }),
    (i.o = function (e, t) {
      return Object.prototype.hasOwnProperty.call(e, t);
    }),
    (i.p = ''),
    i((i.s = 51))
  );
})([
  function (e, t, i) {
    'use strict';
    e.exports = function (e) {
      var t = [];
      return (
        (t.toString = function () {
          return this.map(function (t) {
            var i = (function (e, t) {
              var i = e[1] || '',
                n = e[3];
              if (!n) return i;
              if (t && 'function' == typeof btoa) {
                var o =
                    ((r = n),
                    '/*# sourceMappingURL=data:application/json;charset=utf-8;base64,' +
                      btoa(unescape(encodeURIComponent(JSON.stringify(r)))) +
                      ' */'),
                  a = n.sources.map(function (e) {
                    return '/*# sourceURL=' + n.sourceRoot + e + ' */';
                  });
                return [i].concat(a).concat([o]).join('\n');
              }
              var r;
              return [i].join('\n');
            })(t, e);
            return t[2] ? '@media ' + t[2] + '{' + i + '}' : i;
          }).join('');
        }),
        (t.i = function (e, i) {
          'string' == typeof e && (e = [[null, e, '']]);
          for (var n = {}, o = 0; o < this.length; o++) {
            var a = this[o][0];
            null != a && (n[a] = !0);
          }
          for (o = 0; o < e.length; o++) {
            var r = e[o];
            (null != r[0] && n[r[0]]) ||
              (i && !r[2]
                ? (r[2] = i)
                : i && (r[2] = '(' + r[2] + ') and (' + i + ')'),
              t.push(r));
          }
        }),
        t
      );
    };
  },
  function (e, t, i) {
    var n,
      o,
      a = {},
      r =
        ((n = function () {
          return window && document && document.all && !window.atob;
        }),
        function () {
          return void 0 === o && (o = n.apply(this, arguments)), o;
        }),
      s = function (e, t) {
        return t ? t.querySelector(e) : document.querySelector(e);
      },
      c = (function (e) {
        var t = {};
        return function (e, i) {
          if ('function' == typeof e) return e();
          if (void 0 === t[e]) {
            var n = s.call(this, e, i);
            if (
              window.HTMLIFrameElement &&
              n instanceof window.HTMLIFrameElement
            )
              try {
                n = n.contentDocument.head;
              } catch (e) {
                n = null;
              }
            t[e] = n;
          }
          return t[e];
        };
      })(),
      l = null,
      p = 0,
      d = [],
      u = i(38);
    function m(e, t) {
      for (var i = 0; i < e.length; i++) {
        var n = e[i],
          o = a[n.id];
        if (o) {
          o.refs++;
          for (var r = 0; r < o.parts.length; r++) o.parts[r](n.parts[r]);
          for (; r < n.parts.length; r++) o.parts.push(h(n.parts[r], t));
        } else {
          var s = [];
          for (r = 0; r < n.parts.length; r++) s.push(h(n.parts[r], t));
          a[n.id] = { id: n.id, refs: 1, parts: s };
        }
      }
    }
    function _(e, t) {
      for (var i = [], n = {}, o = 0; o < e.length; o++) {
        var a = e[o],
          r = t.base ? a[0] + t.base : a[0],
          s = { css: a[1], media: a[2], sourceMap: a[3] };
        n[r] ? n[r].parts.push(s) : i.push((n[r] = { id: r, parts: [s] }));
      }
      return i;
    }
    function k(e, t) {
      var i = c(e.insertInto);
      if (!i)
        throw new Error(
          "Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid."
        );
      var n = d[d.length - 1];
      if ('top' === e.insertAt)
        n
          ? n.nextSibling
            ? i.insertBefore(t, n.nextSibling)
            : i.appendChild(t)
          : i.insertBefore(t, i.firstChild),
          d.push(t);
      else if ('bottom' === e.insertAt) i.appendChild(t);
      else {
        if ('object' != typeof e.insertAt || !e.insertAt.before)
          throw new Error(
            "[Style Loader]\n\n Invalid value for parameter 'insertAt' ('options.insertAt') found.\n Must be 'top', 'bottom', or Object.\n (https://github.com/webpack-contrib/style-loader#insertat)\n"
          );
        var o = c(e.insertAt.before, i);
        i.insertBefore(t, o);
      }
    }
    function v(e) {
      if (null === e.parentNode) return !1;
      e.parentNode.removeChild(e);
      var t = d.indexOf(e);
      t >= 0 && d.splice(t, 1);
    }
    function f(e) {
      var t = document.createElement('style');
      if (
        (void 0 === e.attrs.type && (e.attrs.type = 'text/css'),
        void 0 === e.attrs.nonce)
      ) {
        var n = (function () {
          0;
          return i.nc;
        })();
        n && (e.attrs.nonce = n);
      }
      return b(t, e.attrs), k(e, t), t;
    }
    function b(e, t) {
      Object.keys(t).forEach(function (i) {
        e.setAttribute(i, t[i]);
      });
    }
    function h(e, t) {
      var i, n, o, a;
      if (t.transform && e.css) {
        if (
          !(a =
            'function' == typeof t.transform
              ? t.transform(e.css)
              : t.transform.default(e.css))
        )
          return function () {};
        e.css = a;
      }
      if (t.singleton) {
        var r = p++;
        (i = l || (l = f(t))),
          (n = x.bind(null, i, r, !1)),
          (o = x.bind(null, i, r, !0));
      } else
        e.sourceMap &&
        'function' == typeof URL &&
        'function' == typeof URL.createObjectURL &&
        'function' == typeof URL.revokeObjectURL &&
        'function' == typeof Blob &&
        'function' == typeof btoa
          ? ((i = (function (e) {
              var t = document.createElement('link');
              return (
                void 0 === e.attrs.type && (e.attrs.type = 'text/css'),
                (e.attrs.rel = 'stylesheet'),
                b(t, e.attrs),
                k(e, t),
                t
              );
            })(t)),
            (n = z.bind(null, i, t)),
            (o = function () {
              v(i), i.href && URL.revokeObjectURL(i.href);
            }))
          : ((i = f(t)),
            (n = w.bind(null, i)),
            (o = function () {
              v(i);
            }));
      return (
        n(e),
        function (t) {
          if (t) {
            if (
              t.css === e.css &&
              t.media === e.media &&
              t.sourceMap === e.sourceMap
            )
              return;
            n((e = t));
          } else o();
        }
      );
    }
    e.exports = function (e, t) {
      if ('undefined' != typeof DEBUG && DEBUG && 'object' != typeof document)
        throw new Error(
          'The style-loader cannot be used in a non-browser environment'
        );
      ((t = t || {}).attrs = 'object' == typeof t.attrs ? t.attrs : {}),
        t.singleton || 'boolean' == typeof t.singleton || (t.singleton = r()),
        t.insertInto || (t.insertInto = 'head'),
        t.insertAt || (t.insertAt = 'bottom');
      var i = _(e, t);
      return (
        m(i, t),
        function (e) {
          for (var n = [], o = 0; o < i.length; o++) {
            var r = i[o];
            (s = a[r.id]).refs--, n.push(s);
          }
          e && m(_(e, t), t);
          for (o = 0; o < n.length; o++) {
            var s;
            if (0 === (s = n[o]).refs) {
              for (var c = 0; c < s.parts.length; c++) s.parts[c]();
              delete a[s.id];
            }
          }
        }
      );
    };
    var g,
      y =
        ((g = []),
        function (e, t) {
          return (g[e] = t), g.filter(Boolean).join('\n');
        });
    function x(e, t, i, n) {
      var o = i ? '' : n.css;
      if (e.styleSheet) e.styleSheet.cssText = y(t, o);
      else {
        var a = document.createTextNode(o),
          r = e.childNodes;
        r[t] && e.removeChild(r[t]),
          r.length ? e.insertBefore(a, r[t]) : e.appendChild(a);
      }
    }
    function w(e, t) {
      var i = t.css,
        n = t.media;
      if ((n && e.setAttribute('media', n), e.styleSheet))
        e.styleSheet.cssText = i;
      else {
        for (; e.firstChild; ) e.removeChild(e.firstChild);
        e.appendChild(document.createTextNode(i));
      }
    }
    function z(e, t, i) {
      var n = i.css,
        o = i.sourceMap,
        a = void 0 === t.convertToAbsoluteUrls && o;
      (t.convertToAbsoluteUrls || a) && (n = u(n)),
        o &&
          (n +=
            '\n/*# sourceMappingURL=data:application/json;base64,' +
            btoa(unescape(encodeURIComponent(JSON.stringify(o)))) +
            ' */');
      var r = new Blob([n], { type: 'text/css' }),
        s = e.href;
      (e.href = URL.createObjectURL(r)), s && URL.revokeObjectURL(s);
    }
  },
  function (e) {  
    e.exports = {
      i18n: {    
        "active": bon_consent_active,
        "always_active": bon_consent_always_active,
        "impressum": `<a href='%s' target='_blank'>${bon_consent_impressum}</a>`,
        "inactive": bon_consent_inactive,
        "nb_agree": bon_consent_nb_agree,
        "nb_changep": bon_consent_nb_changep,
        "nb_ok": bon_consent_nb_ok,
        "nb_reject": bon_consent_nb_reject,
        "nb_text": bon_consent_nb_text,
        "nb_title": bon_consent_nb_title,
        "pc_fnct_text_1": bon_consent_pc_fnct_text_1,
        "pc_fnct_text_2": bon_consent_pc_fnct_text_2,
        "pc_fnct_text_3": bon_consent_pc_fnct_text_3,
        "pc_minfo_text_1": bon_consent_pc_minfo_text_1,
        "pc_minfo_text_2": bon_consent_pc_minfo_text_2,
        "pc_minfo_text_3": `${bon_consent_pc_minfo_text_3} <a href='%s' target='_blank'>${bon_consent_privacy_policy}</a>.`,
        "pc_save": bon_consent_pc_save,
        "pc_sncssr_text_1": bon_consent_pc_sncssr_text_1,
        "pc_sncssr_text_2": bon_consent_pc_sncssr_text_2,
        "pc_sncssr_text_3": bon_consent_pc_sncssr_text_3,
        "pc_title": bon_consent_pc_title,
        "pc_trck_text_1": bon_consent_pc_trck_text_1,
        "pc_trck_text_2": bon_consent_pc_trck_text_2,
        "pc_trck_text_3": bon_consent_pc_trck_text_3,
        "pc_trck_text_4": bon_consent_pc_trck_text_4,
        "pc_trgt_text_1": bon_consent_pc_trgt_text_1,
        "pc_trgt_text_2": bon_consent_pc_trgt_text_2,
        "pc_trgt_text_3": bon_consent_pc_trgt_text_3,
        "pc_trgt_text_4": bon_consent_pc_trgt_text_4,
        "pc_yprivacy_text_1": bon_consent_pc_yprivacy_text_1,
        "pc_yprivacy_text_2": bon_consent_pc_yprivacy_text_2,
        "pc_yprivacy_text_3": bon_consent_pc_yprivacy_text_3,
        "pc_yprivacy_title": bon_consent_pc_yprivacy_title,
        "privacy_policy": `<a href='%s' target='_blank'>${bon_consent_privacy_policy}</a>`,
      }
    }
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Active","always_active":"Always active","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inactive","nb_agree":"I agree","nb_changep":"Change my preferences","nb_ok":"OK","nb_reject":"I decline","nb_text":"We use cookies and other tracking technologies to improve your browsing experience on our website, to show you personalised content and targeted ads, to analyse our website traffic, and to understand where our visitors are coming from.","nb_title":"We use cookies","pc_fnct_text_1":"Functionality cookies","pc_fnct_text_2":"These cookies are used to provide you with a more personalised experience on our website and to remember choices you make when you use our website.","pc_fnct_text_3":"For example, we may use functionality cookies to remember your language preferences or remember your login details.","pc_minfo_text_1":"More information","pc_minfo_text_2":"For any queries in relation to our policy on cookies and your choices, please contact us.","pc_minfo_text_3":"To find out more, please visit our <a href=\'%s\' target=\'_blank\'>Privacy Policy</a>.","pc_save":"Save my preferences","pc_sncssr_text_1":"Strictly necessary cookies","pc_sncssr_text_2":"These cookies are essential to provide you with services available through our website and to enable you to use certain features of our website.","pc_sncssr_text_3":"Without these cookies, we cannot provide you certain services on our website.","pc_title":"Cookies Preferences Centre","pc_trck_text_1":"Tracking cookies","pc_trck_text_2":"These cookies are used to collect information to analyse the traffic to our website and how visitors are using our website.","pc_trck_text_3":"For example, these cookies may track things such as how long you spend on the website or the pages you visit which helps us to understand how we can improve our website for you.","pc_trck_text_4":"The information collected through these tracking and performance cookies do not identify any individual visitor.","pc_trgt_text_1":"Targeting and advertising cookies","pc_trgt_text_2":"These cookies are used to show advertising that is likely to be of interest to you based on your browsing habits.","pc_trgt_text_3":"These cookies, as served by our content and/or advertising providers, may combine information they collected from our website with other information they have independently collected relating to your web browser\'s activities across their network of websites.","pc_trgt_text_4":"If you choose to remove or disable these targeting or advertising cookies, you will still see adverts but they may not be relevant to you.","pc_yprivacy_text_1":"Your privacy is important to us","pc_yprivacy_text_2":"Cookies are very small text files that are stored on your computer when you visit a website. We use cookies for a variety of purposes and to enhance your online experience on our website (for example, to remember your account login details).","pc_yprivacy_text_3":"You can change your preferences and decline certain types of cookies to be stored on your computer while browsing our website. You can also remove any cookies already stored on your computer, but keep in mind that deleting cookies may prevent you from using parts of our website.","pc_yprivacy_title":"Your privacy","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Privacy Policy</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktiv","always_active":"Immer aktiv","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inaktiv","nb_agree":"Alle akzeptieren","nb_changep":"Einstellungen Г¤ndern","nb_ok":"OK","nb_reject":"Ich lehne ab","nb_text":"Diese Website verwendet Cookies und Targeting Technologien, um Ihnen ein besseres Internet-Erlebnis zu ermГ¶glichen und die Werbung, die Sie sehen, besser an Ihre BedГјrfnisse anzupassen. Diese Technologien nutzen wir auГџerdem, um Ergebnisse zu messen, um zu verstehen, woher unsere Besucher kommen oder um unsere Website weiter zu entwickeln.","nb_title":"Ihre PrivatsphГ¤re ist uns wichtig","pc_fnct_text_1":"Funktions Cookies","pc_fnct_text_2":"Diese Cookies werden verwendet, um Ihnen ein persГ¶nlicheres Erlebnis auf unserer Website zu ermГ¶glichen und um sich an Ihre Entscheidungen zu erinnern, die Sie bei der Nutzung unserer Website getroffen haben.","pc_fnct_text_3":"Beispielsweise kГ¶nnen wir Funktions-Cookies verwenden, um Ihre Spracheinstellungen oder Ihre Anmeldedaten zu speichern.","pc_minfo_text_1":"Mehr Informationen","pc_minfo_text_2":"Bei Fragen in Bezug auf unseren Umgang mit Cookies und Ihrer PrivatsphГ¤re kontaktieren Sie uns bitte.","pc_minfo_text_3":"Details finden Sie in unserer <a href=\'%s\' target=\'_blank\'>Datenschutzrichtlinie</a>.","pc_save":"Einstellungen speichern","pc_sncssr_text_1":"Technisch notwendige Cookies","pc_sncssr_text_2":"Diese Cookies sind fГјr die Bereitstellung von Diensten, die Гјber unsere Website verfГјgbar sind, und fГјr die Verwendung bestimmter Funktionen unserer Website von wesentlicher Bedeutung.","pc_sncssr_text_3":"Ohne diese Cookies kГ¶nnen wir Ihnen bestimmte Dienste auf unserer Website nicht zur VerfГјgung stellen.","pc_title":"Cookie Einstellungen","pc_trck_text_1":"Tracking und Performance Cookies","pc_trck_text_2":"Diese Cookies werden zum Sammeln von Informationen verwendet, um den Verkehr auf unserer Website und die Nutzung unserer Website durch Besucher zu analysieren.","pc_trck_text_3":"Diese Cookies kГ¶nnen beispielsweise nachverfolgen, wie lange Sie auf der Website verweilen oder welche Seiten Sie besuchen. So kГ¶nnen wir verstehen, wie wir unsere Website fГјr Sie verbessern kГ¶nnen.","pc_trck_text_4":"Die durch diese Tracking- und Performance-Cookies gesammelten Informationen identifizieren keinen einzelnen Besucher.","pc_trgt_text_1":"Targeting und Werbung Cookies","pc_trgt_text_2":"Diese Cookies werden genutzt, um Werbung anzuzeigen, die Sie aufgrund Ihrer Surfgewohnheiten wahrscheinlich interessieren wird.","pc_trgt_text_3":"Diese Cookies, die von unseren Inhalten und / oder Werbeanbietern bereitgestellt werden, kГ¶nnen Informationen, die sie von unserer Website gesammelt haben, mit anderen Informationen kombinieren, welche sie durch AktivitГ¤ten Ihres Webbrowsers in Ihrem Netzwerk von Websites gesammelt haben.","pc_trgt_text_4":"Wenn Sie diese Targeting- oder Werbe-Cookies entfernen oder deaktivieren, werden weiterhin Anzeigen angezeigt. Diese sind fГјr Sie jedoch mГ¶glicherweise nicht relevant.","pc_yprivacy_text_1":"Ihre PrivatsphГ¤re ist uns wichtig","pc_yprivacy_text_2":"Cookies sind sehr kleine Textdateien, die auf Ihrem Rechner gespeichert werden, wenn Sie eine Website besuchen. Wir verwenden Cookies fГјr eine Reihe von Auswertungen, um damit Ihren Besuch auf unserer Website kontinuierlich verbessern zu kГ¶nnen (z. B. damit Ihnen Ihre Login-Daten erhalten bleiben).","pc_yprivacy_text_3":"Sie kГ¶nnen Ihre Einstellungen Г¤ndern und verschiedenen Arten von Cookies erlauben, auf Ihrem Rechner gespeichert zu werden, wГ¤hrend Sie unsere Webseite besuchen. Sie kГ¶nnen auf Ihrem Rechner gespeicherte Cookies ebenso weitgehend wieder entfernen. Bitte bedenken Sie aber, dass dadurch Teile unserer Website mГ¶glicherweise nicht mehr in der gedachten Art und Weise nutzbar sind.","pc_yprivacy_title":"Ihre PrivatsphГ¤re","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Datenschutzrichtlinie</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Actif","always_active":"Toujours activГ©","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inactif","nb_agree":"J\'accepte","nb_changep":"Changer mes prГ©fГ©rences","nb_ok":"OK","nb_reject":"Je refuse","nb_text":"Nous utilisons des cookies et d\'autres technologies de suivi pour amГ©liorer votre expГ©rience de navigation sur notre site, pour vous montrer un contenu personnalisГ© et des publicitГ©s ciblГ©es, pour analyser le trafic de notre site et pour comprendre la provenance de nos visiteurs.","nb_title":"Nous utilisons des cookies","pc_fnct_text_1":"Cookies de FonctionnalitГ©","pc_fnct_text_2":"Ces cookies servent Г  vous offrir une expГ©rience plus personnalisГ©e sur notre site Web et Г  mГ©moriser les choix que vous faites lorsque vous utilisez notre site Web.","pc_fnct_text_3":"Par exemple, nous pouvons utiliser des cookies de fonctionnalitГ© pour mГ©moriser vos prГ©fГ©rences de langue ou vos identifiants de connexion.","pc_minfo_text_1":"Plus d\'information","pc_minfo_text_2":"Pour toute question relative Г  notre politique en matiГЁre de cookies et Г  vos choix, veuillez nous contacter.","pc_minfo_text_3":"Pour en savoir plus, merci de consulter notre <a href=\'%s\' target=\'_blank\'>Politique de confidentialitГ©</a>.","pc_save":"Sauvegarder mes prГ©fГ©rences","pc_sncssr_text_1":"Cookies strictement nГ©cessaires","pc_sncssr_text_2":"Ces cookies sont essentiels pour vous fournir les services disponibles sur notre site Web et vous permettre dвЂ™utiliser certaines fonctionnalitГ©s de notre site Web.","pc_sncssr_text_3":"Sans ces cookies, nous ne pouvons pas vous fournir certains services sur notre site Web.","pc_title":"Espace de PrГ©fГ©rences des Cookies","pc_trck_text_1":"Cookies de suivi et de performance","pc_trck_text_2":"Ces cookies sont utilisГ©s pour collecter des informations permettant d\'analyser le trafic sur notre site et la maniГЁre dont les visiteurs utilisent notre site.","pc_trck_text_3":"Par exemple, ces cookies peuvent suivre des choses telles que le temps que vous passez sur le site Web ou les pages que vous visitez, ce qui nous aide Г  comprendre comment nous pouvons amГ©liorer notre site Web pour vous.","pc_trck_text_4":"Les informations collectГ©es via ces cookies de suivi et de performance n\' identifient aucun visiteur en particulier.","pc_trgt_text_1":"Cookies de ciblage et de publicitГ©","pc_trgt_text_2":"Ces cookies sont utilisГ©s pour afficher des publicitГ©s susceptibles de vous intГ©resser en fonction de vos habitudes de navigation.","pc_trgt_text_3":"Ces cookies, tels que servis par nos fournisseurs de contenu et / ou de publicitГ©, peuvent associer des informations qu\'ils ont collectГ©es sur notre site Web Г  d\'autres informations qu\'ils ont collectГ©es de maniГЁre indГ©pendante et concernant les activitГ©s du votre navigateur Web sur son rГ©seau de sites Web.","pc_trgt_text_4":"Si vous choisissez de supprimer ou de dГ©sactiver ces cookies de ciblage ou de publicitГ©, vous verrez toujours des annonces, mais elles risquent de ne pas ГЄtre pertinentes.","pc_yprivacy_text_1":"Votre confidentialitГ© est importante pour nous","pc_yprivacy_text_2":"Les cookies sont de trГЁs petits fichiers texte qui sont stockГ©s sur votre ordinateur lorsque vous visitez un site Web. Nous utilisons des cookies Г  diverses fins et pour amГ©liorer votre expГ©rience en ligne sur notre site Web (par exemple, pour mГ©moriser les informations de connexion de votre compte).","pc_yprivacy_text_3":"Vous pouvez modifier vos prГ©fГ©rences et refuser l\'enregistrement de certains types de cookies sur votre ordinateur lors de la navigation sur notre site. Vous pouvez Г©galement supprimer les cookies dГ©jГ  stockГ©s sur votre ordinateur, mais gardez Г  l\'esprit que leur suppression peut vous empГЄcher d\'utiliser des Г©lГ©ments de notre site Web.","pc_yprivacy_title":"Votre confidentialitГ©","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Politique de confidentialitГ©</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Activo","always_active":"Siempre activo","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inactivo","nb_agree":"Aceptar","nb_changep":"Configurar","nb_ok":"OK","nb_reject":"Renuncio","nb_text":"Usamos cookies y otras tГ©cnicas de rastreo para mejorar tu experiencia de navegaciГіn en nuestra web, para mostrarte contenidos personalizados y anuncios adecuados, para analizar el trГЎfico en nuestra web y para comprender de donde llegan nuestros visitantes.","nb_title":"Utilizamos cookies","pc_fnct_text_1":"Cookies de funcionalidad","pc_fnct_text_2":"Estas cookies son utilizadas para proveerte una experiencia mГЎs personalizada y recordar tus elecciones en nuestra web.","pc_fnct_text_3":"Por ejemplo, podemos utilizar cookies de funcionalidad para recordar tus preferencias de idioma o tus detalles de acceso.","pc_minfo_text_1":"MГЎs informaciГіn","pc_minfo_text_2":"Para cualquier pregunta en relaciГіn con nuestra polГ­tica de cookies y tus preferencias, contacta con nosotros, por favor.","pc_minfo_text_3":"Para saber mГЎs, visita nuestra pГЎgina sobre la <a href=\'%s\' target=\'_blank\'>PolГ­tica de privacidad</a>.","pc_save":"Guardar mis preferencias","pc_sncssr_text_1":"Cookies estrictamente necesarias","pc_sncssr_text_2":"Estos cookies son esenciales para proveerte los servicios disponibles en nuestra web y para permitirte utilizar algunas caracterГ­sticas de nuestra web.","pc_sncssr_text_3":"Sin estas cookies, no podemos proveer algunos servicios de nuestro sitio web.","pc_title":"Centro de Preferencias de Cookies","pc_trck_text_1":"Cookies de rastreo y rendimiento","pc_trck_text_2":"Estas cookies son utilizadas para recopilar informaciГіn, para analizar el trГЎfico y la forma en que los usuarios utilizan nuestra web.","pc_trck_text_3":"Por ejemplo, estas cookies pueden recopilar datos como cuГЎnto tiempo llevas navegado en nuestro sitio web o quГ© pГЎginas visitas, cosa que nos ayuda a comprender cГіmo podemos mejorar nuestra web para ti.","pc_trck_text_4":"La informaciГіn recopilada con estas cookies de rastreo y rendimiento no identifican a ningГєn visitante individual.","pc_trgt_text_1":"Cookies de seguimiento y publicidad","pc_trgt_text_2":"Estas cookies son utilizadas para enseГ±arte anuncios que pueden ser interesantes basados en tus costumbres de navegaciГіn.","pc_trgt_text_3":"Estas cookies, servidas por nuestros proveedores de contenido y/o de publicidad, pueden combinar la informaciГіn que ellos recogieron de nuestro sitio web con otra informaciГіn recopilada por ellos en relaciГіn con las actividades de su navegador a travГ©s de su red de sitios web.","pc_trgt_text_4":"Si eliges cancelar o inhabilitar las cookies de seguimiento y publicidad, seguirГЎs viendo anuncios pero estos podrГ­an no ser de tu interГ©s.","pc_yprivacy_text_1":"Tu privacidad es importante para nosotros","pc_yprivacy_text_2":"Las cookies son pequeГ±os archivos de texto que se almacenan en tu navegador cuando visitas nuestra web. Utilizamos cookies para diferentes objetivos y para mejorar tu experiencia en nuestro sitio web (por ejemplo, para recordar tus detalles de acceso).","pc_yprivacy_text_3":"Puedes cambiar tus preferencias y rechazar que algunos tipos de cookies sean almacenados mientras estГЎs navegando en nuestra web. TambiГ©n puedes cancelar cualquier cookie ya almacenada en tu navegador, pero recuerda que cancelar las cookies puede impedirte utilizar algunas partes de nuestra web.","pc_yprivacy_title":"Tu privacidad","privacy_policy":"<a href=\'%s\' target=\'_blank\'>PolГ­tica de privacidad</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Actiu","always_active":"Sempre actiu","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inactiu","nb_agree":"Estic dвЂ™acord","nb_changep":"Canviar preferГЁncies","nb_ok":"OK","nb_reject":"Declino","nb_text":"Fem servir cookies i altres tecnologies de seguiment per millorar la teva experiГЁncia de navegaciГі al nostre lloc web, per mostrar-te contingut personalitzat i anuncis interessants per a tu, per analitzar el nostre trГ fic i entendre dвЂ™on venen els nostres visitants.","nb_title":"Fem servir cookies","pc_fnct_text_1":"Cookies de funcionalitat","pc_fnct_text_2":"Aquestes cookies ens permeten oferir-vos una experiГЁncia personalitzada i recordar la vostra configuraciГі quan feu servir el nostre lloc web.","pc_fnct_text_3":"Per exemple, podem fer servir funcionalitat per recordar el vostre idioma o les vostres credencials.","pc_minfo_text_1":"MГ©s informaciГі","pc_minfo_text_2":"Per qualsevol pregunta relacionada amb la nostra polГ­tica de cookies i les vostres opcions, si us plau contactiвЂ™ns.","pc_minfo_text_3":"Per saber mГ©s, si us plau visiti la nostra <a href=\'%s\' target=\'_blank\'>PolГ­tica de privacitat</a>.","pc_save":"Guarda les meves preferГЁncies","pc_sncssr_text_1":"Cookies estrictament necessГ ries","pc_sncssr_text_2":"Aquestes cookies sГіn essencials per oferir-vos el nostres serveis i funcionalitats al nostre lloc web.","pc_sncssr_text_3":"Sense aquestes cookies, no us podem oferir alguns serveis.","pc_title":"Centre de PreferГЁncies de Cookies","pc_trck_text_1":"Cookies de seguiment i rendiment","pc_trck_text_2":"Aquestes cookies es fan servir per recollir informaciГі, analitzar el trГ fic i veure com es fa servir el nostre lloc web.","pc_trck_text_3":"Per exemple, aquestes cookies podrien fer el seguiment de quant de temps visiteu el nostre web o quines pГ gines visiteu les quals ens poden ajudar a entendre com millorar el lloc web per vosaltres.","pc_trck_text_4":"La informaciГі recollida grГ cies a aquestes cookies de seguiment i rendiment no us identifiquen de forma individual.","pc_trgt_text_1":"Cookies de publicitat i focalitzaciГі","pc_trgt_text_2":"Aquestes cookies es fan servir per mostrar anuncis que poden ser del vostre interГЁs basats en els vostres hГ bits dвЂ™us.","pc_trgt_text_3":"Aquestes cookies, servides tal i com ho fan els nostres proveГЇdors de publicitat i contingut, poden combinar informaciГі recollida al nostre lloc web amb altra informaciГі que hagin recollit independentment relacionada amb activitat a la seva xarxa de llocs web.","pc_trgt_text_4":"Si vostГЁ decideix eliminar o deshabilitat aquestes cookies, encara veurГ  publicitat perГІ aquesta pot no ser rellevant per vostГЁ.","pc_yprivacy_text_1":"La vostra privacitat Г©s important per nosaltres","pc_yprivacy_text_2":"Les cookies sГіn uns arxius de text molt petits que es guarden al vostre  ordinador quan visiteu un lloc web. Fem servir cookies per una varietat de finalitats i millorar la vostra experiГЁncia al nostre lloc web (per exemple, per recordar les vostres credencials).","pc_yprivacy_text_3":"Pot canviar les vostres preferГЁncies i rebutjar lвЂ™emmagatzematge al vostre ordinador de certs tipus de cookies mentres navega pel nostre. Pot eliminar qualsevol cookie ja emmagatzemada al vostre ordinador, perГІ tingui en compte que eliminar cookies pot impedir que faci servir parts del nostre lloc web.","pc_yprivacy_title":"La vostra privacitat","privacy_policy":"<a href=\'%s\' target=\'_blank\'>PolГ­tica de privacitat</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Attivo","always_active":"Sempre attivo","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inattivo","nb_agree":"Accetto","nb_changep":"Cambia le mie impostazioni","nb_ok":"OK","nb_reject":"Rifiuto","nb_text":"Noi usiamo i cookies e altre tecniche di tracciamento per migliorare la tua esperienza di navigazione nel nostro sito, per mostrarti contenuti personalizzati e annunci mirati, per analizzare il traffico sul nostro sito, e per capire da dove arrivano i nostri visitatori.","nb_title":"Noi usiamo i cookies","pc_fnct_text_1":"Cookies funzionali","pc_fnct_text_2":"Questi cookies sono utilizzati per offrirti unвЂ™esperienza piГ№ personalizzata nel nostro sito e per ricordare le scelte che hai fatto mentre usavi il nostro sito.","pc_fnct_text_3":"Per esempio, possiamo usare cookies funzionali per memorizzare le tue preferenze sulla lingua o i tuoi dettagli di accesso.","pc_minfo_text_1":"PiГ№ informazioni","pc_minfo_text_2":"Per qualsiasi domanda relativa alla nostra politica sui cookies e le tue scelte, per favore contattaci.","pc_minfo_text_3":"Per saperne di piГ№, visita per favore la nostra pagina sulla <a href=\'%s\' target=\'_blank\'>Politica sulla riservatezza</a>.","pc_save":"Salva le mie impostazioni","pc_sncssr_text_1":"Cookies strettamente necessari","pc_sncssr_text_2":"Questi cookies sono essenziali per fornirti i servizi disponibili nel nostro sito e per renderti disponibili alcune funzionalitГ  del nostro sito web.","pc_sncssr_text_3":"Senza questi cookies, non possiamo fornirti alcuni servizi del nostro sito.","pc_title":"Centro Preferenze sui Cookies","pc_trck_text_1":"Cookies di tracciamento e prestazione","pc_trck_text_2":"Questi cookies sono utilizzati per raccogliere informazioni per analizzare il traffico verso il nostro sito e il modo in cui i visitatori utilizzano il nostro sito.","pc_trck_text_3":"Per esempio, questi cookies possono tracciare cose come quanto a lungo ti fermi nel nostro sito o le pagine che visiti, cosa che ci aiuta a capire come possiamo migliorare il nostro sito per te.","pc_trck_text_4":"Le informazioni raccolte attraverso questi cookies di tracciamento e performance non identificano alcun visitatore individuale.","pc_trgt_text_1":"Cookies di targeting e pubblicitГ ","pc_trgt_text_2":"Questi cookies sono usati per mostrare annunci pubblicitari che possano verosimilmente essere di tuo interesse in base alle tue abitudini di navigazione.","pc_trgt_text_3":"Questi cookies, cosГ­ come forniti dai nostri fornitori di  contenuti o annunci pubblicitari, possono combinare le informazioni che raccolgono dal nostro sito web con quelle che hanno indipendentemente raccolto in relazione allвЂ™attivitГ  del tuo browser attraverso la loro rete di siti web.","pc_trgt_text_4":"Se scegli di rimuovere o disabilitare questo tipo di cookies di targeting e pubblicitГ , vedrai ancora annunci pubblicitari ma potrebbero essere irrilevanti per te.","pc_yprivacy_text_1":"La tua privacy ГЁ importante per noi","pc_yprivacy_text_2":"I cookies sono dei piccolissimi file di testo che vengono memorizzati nel tuo computer quando visiti un sito web. Noi usiamo i cookies per una varietГ  di scopi e per migliorare la tua esperienza online nel nostro sito web (per esempio, per ricordare i tuoi dettagli di accesso).","pc_yprivacy_text_3":"Tu puoi cambiare le tue impostazioni e rifiutare che alcuni tipi di cookies vengano memorizzati sul tuo computer mentre stai navigando nel nostro sito web. Puoi anche rimuovere qualsiasi cookie giГ  memorizzato nel tuo computer, ma ricorda che cancellare i cookies puГІ impedirti di utilizzare alcune parti del nostro sito.","pc_yprivacy_title":"La tua privacy","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Politica sulla riservatezza</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktiv","always_active":"Alltid aktiv","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inaktiv","nb_agree":"Jag accepterar","nb_changep":"Г„ndra mina instГ¤llningar","nb_ok":"OK","nb_reject":"Jag avbГ¶jer","nb_text":"Vi anvГ¤nder cookies och andra spГҐrningsteknologier fГ¶r att fГ¶rbГ¤ttra din surfupplevelse pГҐ vГҐr webbplats, fГ¶r att visa dig personligt innehГҐll och riktade annonser, fГ¶r att analysera vГҐr webbplatstrafik och fГ¶r att fГ¶rstГҐ var vГҐra besГ¶kare kommer ifrГҐn.","nb_title":"Vi anvГ¤nder oss av cookies","pc_fnct_text_1":"Funktionella cookies","pc_fnct_text_2":"Dessa cookies anvГ¤nds fГ¶r att ge dig en mer personlig upplevelse pГҐ vГҐr webbplats och fГ¶r att komma ihГҐg val du gГ¶r nГ¤r du anvГ¤nder vГҐr webbplats.","pc_fnct_text_3":"Vi kan till exempel anvГ¤nda funktions cookies fГ¶r att komma ihГҐg dina sprГҐkinstГ¤llningar eller dina inloggningsuppgifter.","pc_minfo_text_1":"Mer information","pc_minfo_text_2":"Kontakta oss om du har frГҐgor angГҐende vГҐr policy om cookies och dina val.","pc_minfo_text_3":"FГ¶r att ta reda pГҐ mer, lГ¤s vГҐr <a href=\'%s\' target=\'_blank\'>integritetspolicy</a>.","pc_save":"Spara mina instГ¤llningar","pc_sncssr_text_1":"Absolut nГ¶dvГ¤ndiga cookies","pc_sncssr_text_2":"Dessa cookies Г¤r viktiga fГ¶r att fГ¶rse dig med tjГ¤nster som Г¤r tillgГ¤ngliga via vГҐr webbplats och fГ¶r att du ska kunna anvГ¤nda vissa funktioner pГҐ vГҐr webbplats.","pc_sncssr_text_3":"Utan dessa cookies kan vi inte tillhandahГҐlla vissa tjГ¤nster pГҐ vГҐr webbplats.","pc_title":"Cookies InstГ¤llningar","pc_trck_text_1":"SpГҐrnings- och prestanda cookies","pc_trck_text_2":"Dessa cookies anvГ¤nds fГ¶r att samla in information fГ¶r att analysera trafiken pГҐ vГҐr webbplats och hur vГҐra besГ¶kare anvГ¤nder den.","pc_trck_text_3":"Dessa cookies kan till exempel spГҐra hur lГ¤nge du spenderar pГҐ webbplatsen eller vilka sidor du besГ¶ker vilket hjГ¤lper oss att fГ¶rstГҐ hur vi kan fГ¶rbГ¤ttra vГҐr webbplats fГ¶r dig.","pc_trck_text_4":"Informationen som samlas in genom dessa spГҐrnings- och prestanda cookies identifierar ingen enskild besГ¶kare.","pc_trgt_text_1":"Inriktnings- och reklamcookies","pc_trgt_text_2":"Dessa cookies anvГ¤nds fГ¶r att visa reklam som sannolikt kommer att vara av intresse fГ¶r dig baserat pГҐ dina surfvanor.","pc_trgt_text_3":"Dessa kakor, som betjГ¤nas av vГҐrt innehГҐll och / eller reklamleverantГ¶rer, kan kombinera information som de samlat in frГҐn vГҐr webbplats med annan information som de har samlat in oberoende om din webblГ¤sares aktiviteter i deras nГ¤tverk av webbplatser.","pc_trgt_text_4":"Om du vГ¤ljer att ta bort eller inaktivera dessa inriktnings- och reklamcookies kommer du fortfarande att se annonser men de kanske inte Г¤r relevanta fГ¶r dig.","pc_yprivacy_text_1":"Din integritet Г¤r viktig fГ¶r oss","pc_yprivacy_text_2":"Cookies Г¤r mycket smГҐ textfiler som lagras pГҐ din dator nГ¤r du besГ¶ker en webbplats. Vi anvГ¤nder cookies till olika Г¤ndamГҐl och fГ¶r att kunna fГ¶rbГ¤ttra din onlineupplevelse pГҐ vГҐr webbplats (till exempel som att komma ihГҐg dina inloggningsuppgifter).","pc_yprivacy_text_3":"Du kan Г¤ndra dina instГ¤llningar och avaktivera vissa typer av cookies som ska lagras pГҐ din dator nГ¤r du surfar pГҐ vГҐr webbplats. Du kan ocksГҐ ta bort alla cookies som redan Г¤r lagrade pГҐ din dator, men kom ihГҐg att radering av cookies kan hindra dig frГҐn att anvГ¤nda delar av vГҐr webbplats.","pc_yprivacy_title":"Din integritet","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Integritetspolicy</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Actief","always_active":"Altijd actief","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inactief","nb_agree":"Ik ga akkoord","nb_changep":"Wijzig mijn voorkeuren","nb_ok":"OK","nb_reject":"Ik weiger","nb_text":"Wij maken gebruik van cookies en andere tracking-technologieГ«n om uw surfervaring op onze website te verbeteren, om gepersonaliseerde inhoud en advertenties te tonen, om ons websiteverkeer te analyseren en om te begrijpen waar onze bezoekers vandaan komen.","nb_title":"Wij gebruiken cookies","pc_fnct_text_1":"Functionele cookies","pc_fnct_text_2":"Deze cookies worden gebruikt om u een persoonlijkere ervaring op onze website te bieden en om keuzes te onthouden die u maakt wanneer u onze website gebruikt.","pc_fnct_text_3":"Functionele cookies worden bijvoorbeeld gebruikt om uw taalvoorkeuren of inloggegevens te onthouden.","pc_minfo_text_1":"Meer informatie","pc_minfo_text_2":"Voor vragen in verband met ons cookiebeleid en uw keuzes kan u ons contacteren.","pc_minfo_text_3":"Voor meer informatie, bezoek ons <a href=\'%s\' target=\'_blank\'>Privacybeleid</a>.","pc_save":"Sla mijn voorkeuren op","pc_sncssr_text_1":"Strikt noodzakelijke cookies","pc_sncssr_text_2":"Deze cookies zijn essentieel om u de diensten aan te bieden die beschikbaar zijn via onze website en om u in staat te stellen bepaalde functies van onze website te gebruiken.","pc_sncssr_text_3":"Zonder deze cookies kunnen we u bepaalde diensten op onze website niet aanbieden.","pc_title":"Cookie instellingen","pc_trck_text_1":"Tracking- en prestatie cookies","pc_trck_text_2":"Deze cookies worden gebruikt om informatie te verzamelen om het verkeer naar onze website te analyseren en hoe bezoekers onze website gebruiken.","pc_trck_text_3":"Deze cookies kunnen gegevens zoals hoe lang u op de website doorbrengt of de pagina\'s die u bezoekt, bijhouden. Dit helpt ons te begrijpen hoe we onze website voor u kunnen verbeteren.","pc_trck_text_4":"Individuele bezoekers kunnen niet geГЇdentificeerd worden aan hand van de informatie in deze cookies.","pc_trgt_text_1":"Targeting- en advertentie cookies","pc_trgt_text_2":"Deze cookies worden gebruikt om advertenties weer te geven die u waarschijnlijk interesseren op basis van uw surfgedrag.","pc_trgt_text_3":"Deze cookies, zoals aangeboden op basis van de inhoud van onze site en/of reclame aanbieders, kunnen informatie die ze van onze website hebben verzameld combineren met andere informatie die ze onafhankelijk hebben verzameld met betrekking tot de activiteiten van uw webbrowser via hun netwerk van websites.","pc_trgt_text_4":"Als u ervoor kiest deze targeting- of advertentiecookies te verwijderen of uit te schakelen, ziet u nog steeds advertenties, maar deze zijn mogelijk niet relevant voor u.","pc_yprivacy_text_1":"Uw privacy is belangrijk voor ons","pc_yprivacy_text_2":"Cookies zijn kleine tekstbestanden die bij het bezoeken van een website op uw computer worden opgeslagen. We gebruiken cookies voor verschillende doeleinden en om uw online ervaring op onze website te verbeteren (bijvoorbeeld om de inloggegevens voor uw account te onthouden).","pc_yprivacy_text_3":"U kunt uw voorkeuren wijzigen en bepaalde soorten cookies weigeren die op uw computer worden opgeslagen tijdens het browsen op onze website. U kunt ook alle cookies verwijderen die al op uw computer zijn opgeslagen, maar houd er rekening mee dat het verwijderen van cookies ertoe kan leiden dat u delen van onze website niet kunt gebruiken.","pc_yprivacy_title":"Jouw privacy","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Privacybeleid</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Ativo","always_active":"Sempre ativo","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inativo","nb_agree":"Concordo","nb_changep":"Alterar as minhas preferГЄncias","nb_ok":"OK","nb_reject":"Eu recuso","nb_text":"Utilizamos cookies e outras tecnologias de mediГ§ГЈo para melhorar a sua experiГЄncia de navegaГ§ГЈo no nosso site, de forma a mostrar conteГєdo personalizado, anГєncios direcionados, analisar o trГЎfego do site e entender de onde vГЄm os visitantes.","nb_title":"O nosso site usa cookies","pc_fnct_text_1":"Cookies de funcionalidade","pc_fnct_text_2":"Estes cookies sГЈo usados вЂ‹вЂ‹para fornecer uma experiГЄncia mais personalizada no nosso site e para lembrar as escolhas que faz ao usar o nosso site.","pc_fnct_text_3":"Por exemplo, podemos usar cookies de funcionalidade para se lembrar das suas preferГЄncias de idioma e/ ou os seus detalhes de login.","pc_minfo_text_1":"Mais InformaГ§Гµes","pc_minfo_text_2":"Para qualquer dГєvida sobre a nossa polГ­tica de cookies e as suas opГ§Гµes, entre em contato connosco.","pc_minfo_text_3":"Para obter mais detalhes, por favor consulte a nossa <a href=\'%s\' target=\'_blank\'>PolГ­tica de Privacidade</a>.","pc_save":"Guardar as minhas preferГЄncias","pc_sncssr_text_1":"Cookies estritamente necessГЎrios","pc_sncssr_text_2":"Estes cookies sГЈo essenciais para fornecer serviГ§os disponГ­veis no nosso site e permitir que possa usar determinados recursos no nosso site.","pc_sncssr_text_3":"Sem estes cookies, nГЈo podemos fornecer certos serviГ§os no nosso site.","pc_title":"Centro de preferГЄncias de cookies","pc_trck_text_1":"Cookies de mediГ§ГЈo e desempenho","pc_trck_text_2":"Estes cookies sГЈo usados вЂ‹вЂ‹para coletar informaГ§Гµes para analisar o trГЎfego no nosso site e entender como Г© que os visitantes estГЈo a usar o nosso site.","pc_trck_text_3":"Por exemplo, estes cookies podem medir fatores como o tempo despendido no site ou as pГЎginas visitadas, isto vai permitir entender como podemos melhorar o nosso site para os utilizadores.","pc_trck_text_4":"As informaГ§Гµes coletadas por meio destes cookies de mediГ§ГЈo e desempenho nГЈo identificam nenhum visitante individual.","pc_trgt_text_1":"Cookies de segmentaГ§ГЈo e publicidade","pc_trgt_text_2":"Estes cookies sГЈo usados вЂ‹вЂ‹para mostrar publicidade que provavelmente lhe pode interessar com base nos seus hГЎbitos e comportamentos de navegaГ§ГЈo.","pc_trgt_text_3":"Estes cookies, servidos pelo nosso conteГєdo e/ ou fornecedores de publicidade, podem combinar as informaГ§Гµes coletadas no nosso site com outras informaГ§Гµes coletadas independentemente relacionadas com as atividades na rede de sites do seu navegador.","pc_trgt_text_4":"Se optar por remover ou desativar estes cookies de segmentaГ§ГЈo ou publicidade, ainda verГЎ anГєncios, mas estes poderГЈo nГЈo ser relevantes para si.","pc_yprivacy_text_1":"A sua privacidade Г© importante para nГіs","pc_yprivacy_text_2":"Cookies sГЈo pequenos arquivos de texto que sГЈo armazenados no seu computador quando visita um site. Utilizamos cookies para diversos fins e para aprimorar sua experiГЄncia no nosso site (por exemplo, para se lembrar dos detalhes de login da sua conta).","pc_yprivacy_text_3":"Pode alterar as suas preferГЄncias e recusar o armazenamento de certos tipos de cookies no seu computador enquanto navega no nosso site. Pode tambГ©m remover todos os cookies jГЎ armazenados no seu computador, mas lembre-se de que a exclusГЈo de cookies pode impedir o uso de determinadas ГЎreas no nosso site.","pc_yprivacy_title":"A sua privacidade","privacy_policy":"<a href=\'%s\' target=\'_blank\'>PolГ­tica de Privacidade</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"PГ¤Г¤llГ¤","always_active":"Aina pГ¤Г¤llГ¤","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Pois pГ¤Г¤ltГ¤","nb_agree":"HyvГ¤ksyn","nb_changep":"Muuta asetuksiani","nb_ok":"OK","nb_reject":"KieltГ¤ydyn","nb_text":"KГ¤ytГ¤mme evГ¤steitГ¤ ja muita seurantateknologioita parantaaksemme kГ¤yttГ¤jГ¤kokemusta verkkosivustollamme, nГ¤yttГ¤Г¤ksemme sinulle personoituja sisГ¤ltГ¶jГ¤ ja mainoksia, analysoidaksemme verkkoliikennettГ¤ sekГ¤ lisГ¤tГ¤ksemme ymmГ¤rrystГ¤mme kГ¤yttГ¤jiemme sijainnista.","nb_title":"KГ¤ytГ¤mme evГ¤steitГ¤","pc_fnct_text_1":"ToiminnallisuusevГ¤steet","pc_fnct_text_2":"NГ¤itГ¤ evГ¤steitГ¤ kГ¤ytetГ¤Г¤n personoidumman kГ¤yttГ¤jГ¤kokemuksen luomiseksi sekГ¤ valintojesi tallentamiseksi sivustollamme.","pc_fnct_text_3":"Esim. voimme kГ¤yttГ¤Г¤ toiminnallisuusevГ¤steitГ¤ muistaaksemme kielivalintasi sekГ¤ kirjautumistietosi.","pc_minfo_text_1":"LisГ¤tietoa","pc_minfo_text_2":"EvГ¤steisiin liittyvissГ¤ kysymyksissГ¤ ole hyvГ¤ ja ota meihin yhteyttГ¤.","pc_minfo_text_3":"Lue lisГ¤Г¤ <a href=\'%s\' target=\'_blank\'>TietosuojakГ¤ytГ¤ntГ¶</a>.","pc_save":"Tallenna asetukseni","pc_sncssr_text_1":"TГ¤rkeГ¤t evГ¤steet","pc_sncssr_text_2":"NГ¤mГ¤ evГ¤steet mahdollistavat verkkosivustomme palveluiden sekГ¤ tiettyjen ominaisuuksien kГ¤yttГ¤misen.","pc_sncssr_text_3":"Ilman nГ¤itГ¤ evГ¤steitГ¤ emme voi tarjota sinulle tiettyjГ¤ palveluita sivustollamme.","pc_title":"EvГ¤steasetukset","pc_trck_text_1":"Seuranta- ja tehokkuusevГ¤steet","pc_trck_text_2":"NГ¤iden evГ¤steiden avulla kerГ¤tГ¤Г¤n tietoa sivustomme liikenteestГ¤ sekГ¤ kГ¤yttГ¶tavoista.","pc_trck_text_3":"Esim. nГ¤mГ¤ evГ¤steet voivat seurata sitГ¤, paljonko aikaa vietГ¤t sivustollamme, mikГ¤ auttaa meitГ¤ parantamaan sivustomme kГ¤yttГ¶kokemusta jatkossa.","pc_trck_text_4":"NГ¤iden evГ¤steiden avulla kerГ¤tty tietoa ei voida yhdistГ¤Г¤ yksittГ¤iseen kГ¤yttГ¤jГ¤Г¤n.","pc_trgt_text_1":"Kohdennus- ja mainosevГ¤steet","pc_trgt_text_2":"NГ¤itГ¤ evГ¤steitГ¤ kГ¤ytetГ¤Г¤n nГ¤yttГ¤mГ¤Г¤n mainoksia, jotka selauskГ¤ytГ¶ksesi perusteella todennГ¤kГ¶isesti kiinnostavat sinua.","pc_trgt_text_3":"NГ¤mГ¤ sisГ¤ltГ¶- ja/tai mainoskumppanimme tarjoamat evГ¤steet voivat yhdistГ¤Г¤ sivustoltamme kerГ¤ttyГ¤ tietoa muilta heidГ¤n verkostoonsa kuuluvilta sivustoilta kerГ¤ttyihin tietoihin.","pc_trgt_text_4":"Jos pГ¤Г¤tГ¤t poistaa tai kytkeГ¤ pois pГ¤Г¤ltГ¤ nГ¤mГ¤ kohdennus- ja mainosevГ¤steet, nГ¤et yhГ¤ mainoksia, mutta ne eivГ¤t vГ¤lttГ¤mГ¤ttГ¤ ole sinulle oleellisia.","pc_yprivacy_text_1":"Yksityisyytesi on meille tГ¤rkeГ¤Г¤","pc_yprivacy_text_2":"EvГ¤steet ovat pieniГ¤ tekstitiedostoja, jotka tallennetaan laitteeseesi verkkosivulla vieraillessasi. KГ¤ytГ¤mme evГ¤steitГ¤ useaan tarkoitukseen ja parantaaksesi kГ¤yttГ¶kokemustasi verkkosivustollamme (esim. muistaaksemme kirjautumistietosi).","pc_yprivacy_text_3":"Voit muuttaa asetuksiasi ja kieltГ¤Г¤ sivustoltamme tiettyjen evГ¤stetyyppien tallentamisen laitteellesi. Voit myГ¶s poistaa minkГ¤ tahansa jo tallennetun evГ¤steen laitteeltasi, mutta huomaathan, ettГ¤ evГ¤steiden poistaminen saattaa estГ¤Г¤ sinua kГ¤yttГ¤mГ¤stГ¤ osaa sivustomme sisГ¤llГ¶stГ¤.","pc_yprivacy_title":"Yksityisyytesi","privacy_policy":"<a href=\'%s\' target=\'_blank\'>TietosuojakГ¤ytГ¤ntГ¶</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"AktГ­v","always_active":"Mindig aktГ­v","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"InaktГ­v","nb_agree":"Elfogadom","nb_changep":"BeГЎllГ­tГЎsok megvГЎltoztatГЎsa","nb_ok":"OK","nb_reject":"ElutasГ­tom","nb_text":"Az oldal sГјtiket Г©s egyГ©b nyomkГ¶vetЕ‘ technolГіgiГЎkat alkalmaz, hogy javГ­tsa a bГ¶ngГ©szГ©si Г©lmГ©nyГ©t, azzal hogy szemГ©lyre szabott tartalmakat Г©s cГ©lzott hirdetГ©seket jelenГ­t meg, Г©s elemzi a weboldalunk forgalmГЎt, hogy megtudjuk honnan Г©rkeztek a lГЎtogatГіink.","nb_title":"Az oldal sГјtiket hasznГЎl","pc_fnct_text_1":"FunkcionГЎlis sГјtik","pc_fnct_text_2":"Ezeket a sГјtiket arra hasznГЎljuk, hogy szemГ©lyre szabottabb Г©lmГ©nyt nyГєjtsunk weboldalunkon, Г©s hogy az oldal rГ¶gzГ­tse a webhelyГјnk hasznГЎlata sorГЎn tett dГ¶ntГ©seket.","pc_fnct_text_3":"PГ©ldГЎul arra hasznГЎlhatunk funkcionГЎlis sГјtiket, hogy emlГ©kezzГјnk a nyelvi beГЎllГ­tГЎsokra, vagy a bejelentkezГ©si adataira.","pc_minfo_text_1":"EgyГ©b informГЎciГіk","pc_minfo_text_2":"A sГјtikre vonatkozГі irГЎnyelveinkkel Г©s az Г–n vГЎlasztГЎsГЎval kapcsolatosan felmerГјlЕ‘ bГЎrmilyen kГ©rdГ©sГ©vel keressen meg bennГјnket.","pc_minfo_text_3":"Ha tГ¶bbet szeretne megtudni, kГ©rjГјk, keresse fel a <a href=\'%s\' target=\'_blank\'>AdatvГ©delmi irГЎnyelvek</a>.","pc_save":"BeГЎllГ­tГЎsok mentГ©se","pc_sncssr_text_1":"FeltГ©tlenГјl szГјksГ©ges sГјtik","pc_sncssr_text_2":"Ezek a sГјtik elengedhetetlenek a weboldalunkon elГ©rhetЕ‘ szolgГЎltatГЎsok nyГєjtГЎsГЎhoz, valamint weboldalunk bizonyos funkciГіinak hasznГЎlatГЎhoz.","pc_sncssr_text_3":"A feltГ©tlenГјl szГјksГ©ges sГјtik hasznГЎlata nГ©lkГјl weboldalunkon nem tudunk bizonyos szolgГЎltatГЎsokat nyГєjtani Г–nnek.","pc_title":"SГјtikre beГЎllГ­tГЎsi kГ¶zpont","pc_trck_text_1":"KГ¶vetГ©si Г©s teljesГ­tmГ©nnyel kapcsolatos sГјtik","pc_trck_text_2":"Ezeket a sГјtiket arra hasznГЎljuk, hogy informГЎciГіkat gyЕ±jtsГјnk weboldalunk forgalmГЎrГіl Г©s lГЎtogatГіirГіl, webhelyГјnk hasznГЎlatГЎnak elemzГ©sГ©hez.","pc_trck_text_3":"PГ©ldГЎul ezek a sГјtik nyomon kГ¶vethetik a webhelyen tГ¶ltГ¶tt idЕ‘t vagy a meglГЎtogatott oldalakat, amely segГ­t megГ©rteni, hogyan javГ­thatjuk webhelyГјnket az Г–n nagyobb megelГ©gedettsГ©gГ©re.","pc_trck_text_4":"Ezekkel a nyomkГ¶vetЕ‘ Г©s teljesГ­tmГ©nnyel kapcsolatos sГјtikkel Г¶sszegyЕ±jtГ¶tt informГЎciГіk egyetlen szemГ©lyt sem azonosГ­tanak.","pc_trgt_text_1":"CГ©lirГЎnyos Г©s hirdetГ©si sГјtik","pc_trgt_text_2":"Ezeket a sГјtiket olyan hirdetГ©sek megjelenГ­tГ©sГ©re hasznГЎljuk, amelyek valГіszГ­nЕ±leg Г©rdekli Г–nt a bГ¶ngГ©szГ©si szokГЎsai alapjГЎn.","pc_trgt_text_3":"Ezek a sГјtik, amelyeket a tartalom Г©s / vagy a reklГЎmszolgГЎltatГіk szolgГЎltatnak, egyesГ­thetik a weboldalunktГіl gyЕ±jtГ¶tt informГЎciГіkat mГЎs informГЎciГіkkal, amelyeket Г¶nГЎllГіan Г¶sszegyЕ±jtГ¶ttek az Г–n bГ¶ngГ©szЕ‘jГ©nek tevГ©kenysГ©geivel kapcsolatban a webhely-hГЎlГіzaton keresztГјl.","pc_trgt_text_4":"Ha Г–n Гєgy dГ¶nt, hogy eltГЎvolГ­tja vagy letiltja ezeket a cГ©lirГЎnyos vagy hirdetГ©si sГјtiket, akkor is lГЎtni fogja a hirdetГ©seket, de lehet, hogy nem lesznek relevГЎnsak az Г–n szГЎmГЎra.","pc_yprivacy_text_1":"Az Г¶n adatainak vГ©delem fontos szГЎmunkra","pc_yprivacy_text_2":"A sГјtik egГ©szen kicsi szГ¶veges fГЎjlok, amelyeket a szГЎmГ­tГіgГ©pГ©n tГЎrolnak, amikor meglГЎtogat egy weboldalt. SГјtiket hasznГЎlunk kГјlГ¶nfГ©le cГ©lokra, Г©s weboldalunkon az online Г©lmГ©ny fokozГЎsa Г©rdekГ©ben (pГ©ldГЎul a fiГіkjГЎnak bejelentkezГ©si adatainak megjegyzГ©sГ©re).","pc_yprivacy_text_3":"WebhelyГјnk bГ¶ngГ©szГ©se kГ¶zben megvГЎltoztathatja a beГЎllГ­tГЎsait, Г©s elutasГ­thatja a szГЎmГ­tГіgГ©pГ©n tГЎrolni kГ­vГЎnt bizonyos tГ­pusГє sГјtik hasznГЎlatГЎt. A szГЎmГ­tГіgГ©pen mГЎr tГЎrolt sГјtiket eltГЎvolГ­thatja, de ne feledje, hogy a sГјtik tГ¶rlГ©se megakadГЎlyozhatja weboldalunk egyes rГ©szeinek hasznГЎlatГЎt.","pc_yprivacy_title":"Az Г¶n adatai vГ©delme","privacy_policy":"<a href=\'%s\' target=\'_blank\'>AdatvГ©delmi irГЎnyelvek</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktivno","always_active":"Uvijek aktivno","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Neaktivno","nb_agree":"SlaЕѕem se","nb_changep":"Promjeni moje postavke","nb_ok":"OK","nb_reject":"Odbijam","nb_text":"Koristimo kolaДЌiД‡e i druge tehnologije praД‡enja da bismo poboljЕЎali vaЕЎe korisniДЌko iskustvo na naЕЎoj web stranici, kako bismo vam prikazali personalizirani sadrЕѕaj i ciljane oglase, analizirali promet na naЕЎoj web stranici i razumjeli odakle dolaze naЕЎi posjetitelji.","nb_title":"Mi koristimo kolaДЌiД‡e","pc_fnct_text_1":"KolaДЌiД‡i funkcionalnosti","pc_fnct_text_2":"Ovi se kolaДЌiД‡i koriste kako bi vam pruЕѕili personalizirano korisniДЌko iskustvo na naЕЎoj web stranici i za pamД‡enje izbora koje napravite kada koristite naЕЎu web stranicu.","pc_fnct_text_3":"Na primjer, moЕѕemo koristiti kolaДЌiД‡e funkcionalnosti da bismo zapamtili vaЕЎe jeziДЌne postavke ili upamtili vaЕЎe podatke za prijavu.","pc_minfo_text_1":"ViЕЎe informacija","pc_minfo_text_2":"Za sve upite vezane uz naЕЎa pravila o kolaДЌiД‡ima i vaЕЎim izborima, molimo da nas kontaktirate.","pc_minfo_text_3":"Da bi saznali viЕЎe, posjetite naЕЎa <a href=\'%s\' target=\'_blank\'>Pravila o privatnosti</a>.","pc_save":"Spremi moje postavke","pc_sncssr_text_1":"Strogo potrebni kolaДЌiД‡i","pc_sncssr_text_2":"Ovi su kolaДЌiД‡i neophodni za pruЕѕanje usluga dostupnih putem naЕЎe web stranice i omoguД‡avanje koriЕЎtenja odreД‘enih znaДЌajki naЕЎe web stranice.","pc_sncssr_text_3":"Bez ovih kolaДЌiД‡a ne moЕѕemo vam pruЕѕiti odreД‘ene usluge na naЕЎoj web stranici.","pc_title":"Centar za postavke kolaДЌiД‡a","pc_trck_text_1":"KolaДЌiД‡i za praД‡enje i performanse","pc_trck_text_2":"Ovi se kolaДЌiД‡i koriste za prikupljanje podataka za analizu prometa na naЕЎoj web stranici i za informaciju kako posjetitelji koriste naЕЎu web stranicu.","pc_trck_text_3":"Na primjer, ti kolaДЌiД‡i mogu pratiti stvari poput dugovanja na web stranici ili stranicama koje posjetite ЕЎto nam pomaЕѕe da shvatimo kako moЕѕemo poboljЕЎati vaЕЎe korisniДЌko iskustvo na naЕЎoj web stranici.","pc_trck_text_4":"Informacije prikupljene ovim praД‡enjem i kolaДЌiД‡i izvedbe ne identificiraju nijednog pojedinaДЌnog posjetitelja.","pc_trgt_text_1":"KolaДЌiД‡i za ciljano oglaЕЎavanje","pc_trgt_text_2":"Ovi se kolaДЌiД‡i koriste za prikazivanje oglasa koji bi vas mogli zanimati na temelju vaЕЎih navika pregledavanja web stranica.","pc_trgt_text_3":"Ovi kolaДЌiД‡i, posluЕѕeni od naЕЎih pruЕѕatelja sadrЕѕaja i / ili oglaЕЎavanja, mogu kombinirati podatke koje su prikupili s naЕЎe web stranice s drugim podacima koje su neovisno prikupili, a odnose se na aktivnosti vaЕЎeg web preglednika kroz njihovu mreЕѕu web stranica.","pc_trgt_text_4":"Ako odluДЌite ukloniti ili onemoguД‡iti ove kolaДЌiД‡e za ciljano oglaЕЎavanje, i dalje Д‡ete vidjeti oglase, ali oni moЕѕda nisu relevantni za vas.","pc_yprivacy_text_1":"VaЕЎa privatnost nam je vaЕѕna","pc_yprivacy_text_2":"KolaДЌiД‡i su vrlo male tekstualne datoteke koje se pohranjuju na vaЕЎem raДЌunalu kada posjetite web stranicu. Mi koristimo kolaДЌiД‡e za razliДЌite svrhe i za poboljЕЎanje vaЕЎeg mreЕѕnog iskustva na naЕЎoj web stranici (na primjer, za pamД‡enje podataka za prijavu na vaЕЎ korisniДЌki raДЌun).","pc_yprivacy_text_3":"MoЕѕete promijeniti svoje postavke i odbiti odreД‘ene vrste kolaДЌiД‡a koji Д‡e se pohraniti na vaЕЎem raДЌunalu tijekom pregledavanja naЕЎe web stranice. TakoД‘er moЕѕete ukloniti sve kolaДЌiД‡e koji su veД‡ pohranjeni na vaЕЎem raДЌunalu, ali imajte na umu da vas brisanje kolaДЌiД‡a moЕѕe sprijeДЌiti da koristite dijelove naЕЎe web stranice.","pc_yprivacy_title":"VaЕЎa privatnost","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Pravila o privatnosti</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"AktivnГ­","always_active":"VЕѕdy aktivnГ­","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"NeaktivnГ­","nb_agree":"SouhlasГ­m","nb_changep":"Upravit mГ© pЕ™edvolby","nb_ok":"OK","nb_reject":"OdmГ­tГЎm","nb_text":"Tyto webovГ© strГЎnky pouЕѕГ­vajГ­ soubory cookies a dalЕЎГ­ sledovacГ­ nГЎstroje s cГ­lem vylepЕЎenГ­ uЕѕivatelskГ©ho prostЕ™edГ­, zobrazenГ­ pЕ™izpЕЇsobenГ©ho obsahu a  reklam, analГЅzy nГЎvЕЎtД›vnosti webovГЅch strГЎnek a zjiЕЎtД›nГ­ zdroje nГЎvЕЎtД›vnosti.","nb_title":"PouЕѕГ­vГЎme soubory cookies","pc_fnct_text_1":"Cookies pro funkcionality","pc_fnct_text_2":"Tyto soubory cookie se pouЕѕГ­vajГ­ k tomu, aby vГЎm na naЕЎich webovГЅch strГЎnkГЎch poskytovaly personalizovanГЅ uЕѕivatelskГЅ zГЎЕѕitek a aby si pamatovaly vaЕЎe volby, kterГ© jste pouЕѕili pЕ™i pouЕѕГ­vГЎnГ­ naЕЎich webovГЅch strГЎnek.","pc_fnct_text_3":"MЕЇЕѕeme napЕ™Г­klad pouЕѕГ­vat soubory cookie k zapamatovГЎnГ­ vaЕЎeho jazyka nebo k zapamatovГЎnГ­ vaЕЎich pЕ™ihlaЕЎovacГ­ch ГєdajЕЇ.","pc_minfo_text_1":"DalЕЎГ­ informace","pc_minfo_text_2":"V pЕ™Г­padД› jakГЅchkoliv dotazЕЇ  ohlednД› naЕЎich zГЎsad tГЅkajГ­cГ­ch se souborЕЇ cookie a vaЕЎich moЕѕnostГ­ nГЎs prosГ­m kontaktujte.","pc_minfo_text_3":"Pro vГ­ce informacГ­ navЕЎtivte naЕЎi strГЎnku <a href=\'%s\' target=\'_blank\'>ZГЎsady ochrany osobnГ­ch ГєdajЕЇ</a>.","pc_save":"UloЕѕit mГ© pЕ™edvolby","pc_sncssr_text_1":"BezpodmГ­neДЌnД› nutnГ© soubory cookies","pc_sncssr_text_2":"Tyto soubory cookies jsou nezbytnГ© k tomu, abychom vГЎm mohli poskytovat sluЕѕby dostupnГ© prostЕ™ednictvГ­m naЕЎeho webu a abychom vГЎm umoЕѕnili pouЕѕГ­vat urДЌitГ© funkce naЕЎeho webu.","pc_sncssr_text_3":"Bez tД›chto cookies vГЎm nemЕЇЕѕeme na naЕЎГ­ webovГ© strГЎnce poskytovat urДЌitГ© sluЕѕby.","pc_title":"Centrum pЕ™edvoleb souborЕЇ Cookies","pc_trck_text_1":"SledovacГ­ a vГЅkonnostnГ­ soubory cookies","pc_trck_text_2":"Tyto soubory cookies se pouЕѕГ­vajГ­ ke shromaЕѕДЏovГЎnГ­ informacГ­ pro analГЅzu provozu na naЕЎich webovГЅch strГЎnkГЎch a sledovГЎnГ­ pouЕѕГ­vГЎnГ­ naЕЎich webovГЅch strГЎnek uЕѕivateli.","pc_trck_text_3":"Tyto soubory cookies mohou napЕ™Г­klad sledovat vД›ci jako je doba kterou na webu trГЎvГ­te, nebo strГЎnky, kterГ© navЕЎtД›vujete, coЕѕ nГЎm pomГЎhГЎ pochopit, jak pro vГЎs mЕЇЕѕeme vylepЕЎit nГЎЕЎ web.","pc_trck_text_4":"Informace shromГЎЕѕdД›nГ© prostЕ™ednictvГ­m tД›chto sledovacГ­ch a vГЅkonnostnГ­ch cookies neidentifikujГ­ ЕѕГЎdnГ© osoby.","pc_trgt_text_1":"Cookies pro cГ­lenГ­ a reklamu","pc_trgt_text_2":"Tyto soubory cookie se pouЕѕГ­vajГ­ k zobrazovГЎnГ­ reklamy, kterГЎ vГЎs pravdД›podobnД› bude zajГ­mat na zГЎkladД› vaЕЎich zvykЕЇ pЕ™i prochГЎzenГ­.","pc_trgt_text_3":"Tyto soubory cookie, jsou poЕѕadovГЎny nГЎmi/nebo poskytovateli reklam, mohou kombinovat informace shromГЎЕѕdД›nГ© z naЕЎich webovГЅch strГЎnek s dalЕЎГ­mi informacemi, kterГ© nezГЎvisle shromГЎЕѕdily z jinГЅch webovГЅch strГЎnek, tГЅkajГ­cГ­ se ДЌinnostГ­ vaЕЎeho internetovГ©ho prohlГ­ЕѕeДЌe v rГЎmci jejich reklamnГ­ sГ­tД› webovГЅch strГЎnek.","pc_trgt_text_4":"Pokud se rozhodnete tyto soubory cookies pro cГ­lenГ­ nebo reklamu odstranit nebo deaktivovat, budou se vГЎm reklamy stГЎle zobrazovat, ale nemusГ­ pro vГЎs bГЅt nadГЎle personalizovanГ© a relevantnГ­.","pc_yprivacy_text_1":"VaЕЎe soukromГ­ je pro nГЎs dЕЇleЕѕitГ©","pc_yprivacy_text_2":"Soubory cookies jsou velmi malГ© textovГ© soubory, kterГ© se uklГЎdajГ­ do vaЕЎeho zaЕ™Г­zenГ­ pЕ™i navЕЎtД›vovГЎnГ­ webovГЅch strГЎnek. Soubory Cookies pouЕѕГ­vГЎme pro rЕЇznГ© ГєДЌely a pro vylepЕЎenГ­ vaЕЎeho online zГЎЕѕitku na webovГ© strГЎnce (napЕ™Г­klad pro zapamatovГЎnГ­ pЕ™ihlaЕЎovacГ­ch ГєdajЕЇ k vaЕЎemu ГєДЌtu).","pc_yprivacy_text_3":"PЕ™i prochГЎzenГ­ naЕЎich webovГЅch strГЎnek mЕЇЕѕete zmД›nit svГ© pЕ™edvolby a odmГ­tnout urДЌitГ© typy cookies, kterГ© se majГ­ uklГЎdat do vaЕЎeho poДЌГ­taДЌe. MЕЇЕѕete takГ© odstranit vЕЎechny soubory cookie, kterГ© jsou jiЕѕ uloЕѕeny ve vaЕЎem poДЌГ­taДЌi, ale mД›jte na pamД›ti, Еѕe odstranД›nГ­ souborЕЇ cookie vГЎm mЕЇЕѕe zabrГЎnit v pouЕѕГ­vГЎnГ­ ДЌГЎstГ­ naЕЎeho webu.","pc_yprivacy_title":"VaЕЎe soukromГ­","privacy_policy":"<a href=\'%s\' target=\'_blank\'>ZГЎsady ochrany osobnГ­ch ГєdajЕЇ</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktiv","always_active":"Altid aktiv","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inaktiv","nb_agree":"Jeg accepterer","nb_changep":"Skift indstillinger","nb_ok":"OK","nb_reject":"Jeg nГ¦gter","nb_text":"Vi bruger cookies og andre tracking teknologier for at forbedre din oplevelse pГҐ vores website, til at vise personaliseret indhold, mГҐlrettede annoncer og til at forstГҐ hvor vores besГёgende kommer fra.","nb_title":"Vi bruger cookies","pc_fnct_text_1":"Funktions cookies","pc_fnct_text_2":"Disse cookies anvendes for at kunne give dig en personaliseret oplevelse af vores hjemmeside, og for at kunne huske valg du har truffet.","pc_fnct_text_3":"Eksempelvis kan vi bruge funktions cookies til at huske sprog-indstillinger eller dine login informationer.","pc_minfo_text_1":"Mere information","pc_minfo_text_2":"Har du spГёrgsmГҐl vedr. vores cookiepolitik og dine valgmuligheder, sГҐ kontakt os venligst.","pc_minfo_text_3":"For at finde ud af mere, sГҐ lГ¦s venligst vores <a href=\'%s\' target=\'_blank\'>Fortrolighedspolitik</a>.","pc_save":"Gem mine indstillinger","pc_sncssr_text_1":"NГёdvendige cookies","pc_sncssr_text_2":"Disse Cookies er essentielle for at du kan bruge vores hjemmeside.","pc_sncssr_text_3":"Uden disse cookies kan vi ikke garantere vores hjemmeside virker ordentligt.","pc_title":"Cookie indstillinger","pc_trck_text_1":"Tracking og performance cookies","pc_trck_text_2":"Disse cookies anvendes til at analysere besГёg pГҐ vores hjemmeside, og hvordan du bruger vores hjemmeside.","pc_trck_text_3":"Eksempelvis kan vi tracke hvor lang tid du bruger hjemmesiden, eller hvilke sider du kigger pГҐ. Det hjГ¦lper os til at forstГҐ hvordan vi kan forbedre hjemmesiden.","pc_trck_text_4":"Informationerne kan ikke identificere dig som individ og er derfor anonyme.","pc_trgt_text_1":"MГҐlretning og annoncecookies","pc_trgt_text_2":"Disse cookies anvendes for at kunne vise annoncer, som sandsynligvis er interessante for dig, baseret pГҐ dine browser profil.","pc_trgt_text_3":"Disse cookies, som sГ¦ttes af vores indhold og/eller annoncepartnere, kan kombinere information fra flere hjemmesider i hele det netvГ¦rk som partnerne styrer.","pc_trgt_text_4":"Hvis du deaktiverer denne indstilling vil du fortsat se reklamer, men de vil ikke lГ¦ngere vГ¦re mГҐlrettet til dig.","pc_yprivacy_text_1":"Dit privatliv er vigtigt for os","pc_yprivacy_text_2":"Cookies er en lille tekstfil, som gemmes pГҐ din computer, nГҐr du besГёger et website. Vi bruger cookies til en rГ¦kke formГҐl, og for at forbedre din oplevelse pГҐ vores website (eksempelvis for at huske dine login oplysninger).","pc_yprivacy_text_3":"Du kan Г¦ndre dine indstillinger og afvise forskellige typer cookies, som gemmes pГҐ din computer, nГҐr du besГёger vores website. Du kan ogsГҐ fjerne cookies som allerede er gemt pГҐ din computer, men bemГ¦rk venligst at sletning af cookies kan betyde der er dele af hjemmesiden som ikke virker.","pc_yprivacy_title":"Dit privatliv","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Fortrolighedspolitik</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Active","always_active":"ГЋntotdeauna active","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inactive","nb_agree":"Sunt de acord","nb_changep":"Vreau sДѓ schimb setДѓrile","nb_ok":"OK","nb_reject":"Refuz","nb_text":"Folosim cookie-uri И™i alte tehnologii de urmДѓrire pentru a Г®mbunДѓtДѓИ›i experienИ›a ta de navigare pe website-ul nostru, pentru afiИ™a conИ›inut И™i reclame personalizate, pentru a analiza traficul de pe website-ul nostru И™i pentru a Г®nИ›elege de unde vin vizitatorii noИ™tri.","nb_title":"Folosim cookie-uri","pc_fnct_text_1":"Cookie-uri funcИ›ionale","pc_fnct_text_2":"Aceste cookie-uri sunt folosite pentru a-И›i asigura o experienИ›Дѓ personalizatДѓ pe website-ul nostru И™i pentru salvarea alegerilor pe care le faci cГўnd foloseИ™ti website-ul nostru.","pc_fnct_text_3":"De exemplu, putem folosi cookie-uri funcИ›ionale pentru a salva preferinИ›ele tale legate de limba website-ului nostru sau datele de logare.","pc_minfo_text_1":"Mai multe informaИ›ii","pc_minfo_text_2":"Pentru mai multe informaИ›ii cu privire la politica noastrДѓ de cookie-uri И™i preferinИ›ele tale, te rugДѓm sДѓ ne contactezi.","pc_minfo_text_3":"Pentru a afla mai multe, te rugДѓm sДѓ citeИ™ti <a href=\'%s\' target=\'_blank\'>Politica noastrДѓ de confidenИ›ialitate</a>.","pc_save":"SalveazДѓ","pc_sncssr_text_1":"Cookie-uri strict necesare","pc_sncssr_text_2":"Aceste cookie-uri sunt esenИ›iale pentru a putea beneficia de serviciile disponibile pe website-ul nostru.","pc_sncssr_text_3":"FДѓrДѓ aceste cookie-uri nu poИ›i folosi anumite funcИ›ionalitДѓИ›i ale website-ului nostru.","pc_title":"PreferinИ›e pentru Cookie-uri","pc_trck_text_1":"Cookie-uri de analizДѓ И™i performanИ›Дѓ","pc_trck_text_2":"Acest tip de cookie-uri sunt folosite pentru a colecta informaИ›ii Г®n vederea analizДѓrii traficului pe website-ul nostru И™i modul Г®n care vizitatorii noИ™tri folosesc website-ul.","pc_trck_text_3":"De exemplu, aceste cookie-uri pot urmДѓri cГўt timp petreci pe website sau paginile pe care le vizitezi, ceea ce ne ajutДѓ sДѓ Г®nИ›elegem cum putem Г®mbunДѓtДѓИ›i website-ul pentru tine.","pc_trck_text_4":"InformaИ›iile astfel colectate nu identificДѓ individual vizitatorii.","pc_trgt_text_1":"Cookie-uri pentru marketing И™i publicitate","pc_trgt_text_2":"Aceste cookie-uri sunt folosite pentru a-И›i afiИ™a reclame cГўt mai pe interesul tДѓu, Г®n funcИ›ie de obiceiurile tale de navigare.","pc_trgt_text_3":"Aceste cookie-uri, aИ™a cum sunt afiИ™ate de furnizori noИ™tri de conИ›inut И™i/sau publicitate, pot combina informaИ›ii de pe website-ul nostru cu alte informaИ›ii pe care furnizori noИ™tri le-au colectat Г®n mod independent cu privire la activitatea ta Г®n reИ›eaua lor de website-uri.","pc_trgt_text_4":"DacДѓ alegi sДѓ И™tergi sau sДѓ dezactivezi aceste cookie-uri tot vei vedea reclame, dar se poate ca aceste reclame sДѓ nu fie relevante pentru tine.","pc_yprivacy_text_1":"ConfidenИ›ialitatea ta este importantДѓ pentru noi","pc_yprivacy_text_2":"Cookie-urile sunt fiИ™iere text foarte mici ce sunt salvate Г®n browser-ul tДѓu atunci cГўnd vizitezi un website. Folosim cookie-uri pentru mai multe scopuri, dar И™i pentru a Г®И›i oferi cea mai bunДѓ experienИ›Дѓ de utilizare posibilДѓ (de exemplu, sДѓ reИ›inem datele tale de logare Г®n cont).","pc_yprivacy_text_3":"ГЋИ›i poИ›i modifica preferinИ›ele И™i poИ›i refuza ca anumite tipuri de cookie-uri sДѓ nu fie salvate Г®n browser Г®n timp ce navigezi pe website-ul nostru. Deasemenea poИ›i И™terge cookie-urile salvate deja Г®n browser, dar reИ›ine cДѓ este posibil sДѓ nu poИ›i folosi anumite pДѓrИ›i ale website-ul nostru Г®n acest caz.","pc_yprivacy_title":"ConfidenИ›ialitatea ta","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Politica noastrДѓ de confidenИ›ialitate</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"AktГ­vne","always_active":"VЕѕdy aktГ­vne","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"NeaktГ­vne","nb_agree":"SГєhlasГ­m","nb_changep":"ZmeniЕҐ moje nastavenia","nb_ok":"OK","nb_reject":"Odmietam","nb_text":"SГєbory cookie a ДЏalЕЎie technolГіgie sledovania pouЕѕГ­vame na zlepЕЎenie vГЎЕЎho zГЎЕѕitku z prehliadania naЕЎich webovГЅch strГЎnok, na to, aby sme vГЎm zobrazovali prispГґsobenГЅ obsah a cielenГ© reklamy, na analГЅzu nГЎvЕЎtevnosti naЕЎich webovГЅch strГЎnok a na pochopenie toho, odkiaДѕ naЕЎi nГЎvЕЎtevnГ­ci prichГЎdzajГє.","nb_title":"PouЕѕГ­vame cookies","pc_fnct_text_1":"FunkДЌnГ© cookies","pc_fnct_text_2":"Tieto sГєbory cookie sa pouЕѕГ­vajГє na to, aby vГЎm poskytli osobnejЕЎie prostredie na naЕЎej webovej strГЎnke, a na zapamГ¤tanie si rozhodnutГ­, ktorГ© urobГ­te pri pouЕѕГ­vanГ­ naЕЎej webovej strГЎnky.","pc_fnct_text_3":"NaprГ­klad mГґЕѕeme pouЕѕiЕҐ funkДЌnГ© cookies na zapamГ¤tanie vaЕЎich jazykovГЅch preferenciГ­ alebo na zapamГ¤tanie vaЕЎich prihlasovacГ­ch Гєdajov.","pc_minfo_text_1":"Viac informГЎciГ­","pc_minfo_text_2":"Ak mГЎte akГ©koДѕvek otГЎzky tГЅkajГєce sa naЕЎich zГЎsad tГЅkajГєcich sa sГєborov cookie a vaЕЎich moЕѕnostГ­, kontaktujte nГЎs.","pc_minfo_text_3":"Ak sa chcete dozvedieЕҐ viac, navЕЎtГ­vte <a href=\'%s\' target=\'_blank\'>ZГЎsady ochrany osobnГЅch Гєdajov</a>.","pc_save":"UloЕѕ moje predvoДѕby","pc_sncssr_text_1":"Nevyhnutne potrebnГ© cookies","pc_sncssr_text_2":"Tieto sГєbory cookie sГє nevyhnutnГ© na to, aby sme vГЎm mohli poskytovaЕҐ sluЕѕby dostupnГ© prostrednГ­ctvom naЕЎej webovej strГЎnky a aby ste mohli pouЕѕГ­vaЕҐ urДЌitГ© funkcie naЕЎej webovej strГЎnky.","pc_sncssr_text_3":"Bez tГЅchto sГєborov cookie vГЎm nemГґЕѕeme poskytnГєЕҐ urДЌitГ© sluЕѕby na naЕЎom webe.","pc_title":"Centrum predvolieb cookies","pc_trck_text_1":"Sledovacie a vГЅkonnostnГ© cookies","pc_trck_text_2":"Tieto sГєbory cookie sa pouЕѕГ­vajГє na zhromaЕѕДЏovanie informГЎciГ­ na analГЅzu prenosu na naЕЎom webe a toho, ako nГЎvЕЎtevnГ­ci pouЕѕГ­vajГє nГЎЕЎ web.","pc_trck_text_3":"Tieto sГєbory cookie mГґЕѕu naprГ­klad sledovaЕҐ naprГ­klad to, koДѕko ДЌasu strГЎvite na webovГЅch strГЎnkach alebo navЕЎtГ­venГЅch strГЎnkach, ДЌo nГЎm pomГЎha pochopiЕҐ, ako mГґЕѕeme pre vГЎs vylepЕЎiЕҐ naЕЎe webovГ© strГЎnky.","pc_trck_text_4":"InformГЎcie zhromaЕѕdenГ© prostrednГ­ctvom tГЅchto sГєborov cookie na sledovanie a vГЅkonnosЕҐ neidentifikujГє Еѕiadneho jednotlivГ©ho nГЎvЕЎtevnГ­ka.","pc_trgt_text_1":"Zacielenie a reklamnГ© cookies","pc_trgt_text_2":"Tieto sГєbory cookie sa pouЕѕГ­vajГє na zobrazovanie reklГЎm, ktorГ© by vГЎs mohli pravdepodobne zaujГ­maЕҐ na zГЎklade vaЕЎich zvykov pri prehliadanГ­.","pc_trgt_text_3":"Tieto sГєbory cookie, ktorГ© slГєЕѕia pre nГЎЕЎ obsah a/alebo poskytovateДѕov reklГЎm, mГґЕѕu kombinovaЕҐ informГЎcie zhromaЕѕdenГ© z naЕЎej webovej strГЎnky s ДЏalЕЎГ­mi informГЎciami, ktorГ© nezГЎvisle zhromaЕѕdili, tГЅkajГєce sa aktivГ­t vГЎЕЎho webovГ©ho prehliadaДЌa v rГЎmci ich siete webovГЅch strГЎnok.","pc_trgt_text_4":"Ak sa rozhodnete odstrГЎniЕҐ alebo zakГЎzaЕҐ tieto sГєbory cookie pre zacielenie alebo reklamu, stГЎle sa vГЎm budГє zobrazovaЕҐ reklamy, ktorГ© vЕЎak pre vГЎs nemusia byЕҐ relevantnГ©.","pc_yprivacy_text_1":"VaЕЎe sГєkromie je pre nГЎs dГґleЕѕitГ©","pc_yprivacy_text_2":"SГєbory cookie sГє veДѕmi malГ© textovГ© sГєbory, ktorГ© sa ukladajГє do vГЎЕЎho poДЌГ­taДЌa pri nГЎvЕЎteve webovej strГЎnky. SГєbory cookie pouЕѕГ­vame na rГґzne ГєДЌely a na zlepЕЎenie vГЎЕЎho online zГЎЕѕitku z naЕЎej webovej strГЎnky (naprГ­klad na zapamГ¤tanie prihlasovacГ­ch Гєdajov vГЎЕЎho ГєДЌtu).","pc_yprivacy_text_3":"MГґЕѕete zmeniЕҐ svoje predvoДѕby a odmietnuЕҐ urДЌitГ© typy sГєborov cookie, ktorГ© sa majГє ukladaЕҐ vo vaЕЎom poДЌГ­taДЌi pri prehliadanГ­ naЕЎich webovГЅch strГЎnok. MГґЕѕete tieЕѕ odstrГЎniЕҐ vЕЎetky sГєbory cookie, ktorГ© sГє uЕѕ uloЕѕenГ© vo vaЕЎom poДЌГ­taДЌi, ale nezabudnite, Еѕe vymazanie sГєborov cookie vГЎm mГґЕѕe zabrГЎniЕҐ v pouЕѕГ­vanГ­ ДЌastГ­ naЕЎej webovej strГЎnky.","pc_yprivacy_title":"VaЕЎe sГєkromie","privacy_policy":"<a href=\'%s\' target=\'_blank\'>ZГЎsady ochrany osobnГЅch Гєdajov</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktivni","always_active":"Vedno aktivni","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Neaktivni","nb_agree":"Se strinjam","nb_changep":"Spremeni moje nastavitve","nb_ok":"V redu","nb_reject":"ZavraДЌam","nb_text":"PiЕЎkotke in druge sledilne tehnologije uporabljamo za izboljЕЎanje vaЕЎe uporabniЕЎke izkuЕЎnje med brskanjem po naЕЎi spletni strani, za  prikazovanje personaliziranih vsebin oz. targetiranih oglasov, za analizo obiskov naЕЎe spletne strani in za vpogled v to, iz kje prihajajo naЕЎi gostje.","nb_title":"Uporabljamo piЕЎkotke","pc_fnct_text_1":"Funkcionalni piЕЎkotki (ang. functionality cookies)","pc_fnct_text_2":"Ti piЕЎkotki se uporabljajo za zagotavljanje bolj personalizirane izkuЕЎnje na naЕЎi spletni strani in za shranjevanje vaЕЎih odloДЌitev ob uporabi naЕЎe spletne strani.","pc_fnct_text_3":"Funkcionalne piЕЎkotke lahko, na primer, uporabljamo za to, da si zapomnimo vaЕЎe jezikovne nastavitve oz. podatke za vpis v vaЕЎ raДЌun.","pc_minfo_text_1":"VeДЌ informacij","pc_minfo_text_2":"ДЊe imate kakrЕЎnakoli vpraЕЎanja v zvezi z naЕЎim pravilnikom o piЕЎkotkih in vaЕЎih izbirah, nas prosim kontaktirajte.","pc_minfo_text_3":"Za veДЌ informacij si prosim oglejte naЕЎ <a href=\'%s\' target=\'_blank\'>Politika zasebnosti</a>.","pc_save":"Shrani moje nastavitve","pc_sncssr_text_1":"Nujno potrebni piЕЎkotki (ang. strictly necessary cookies)","pc_sncssr_text_2":"Ti piЕЎkotki so kljuДЌnega pomena pri zagotavljanju storitev, ki so na voljo na naЕЎi spletni strani, in pri omogoДЌanju doloДЌenih funkcionalnosti naЕЎe spletne strani.","pc_sncssr_text_3":"Brez teh piЕЎkotkov vam ne moremo zagotoviti doloДЌenih storitev na naЕЎi spletni strani.","pc_title":"Nastavitve piЕЎkotkov","pc_trck_text_1":"Sledilni in izvedbeni piЕЎkotki (ang. tracking and performance cookies)","pc_trck_text_2":"Ti piЕЎkotki se uporabljajo za zbiranje podatkov za analizo obiskov naЕЎe spletne strani in vpogled v to, kako gostje uporabljajo naЕЎo spletno stran.","pc_trck_text_3":"Ti piЕЎkotki lahko, na primer, spremljajo stvari kot so to, koliko ДЌasa preЕѕivite na naЕЎi spletni strani oz. katere strani obiЕЎДЌete, kar nam pomaga pri razumevanju, kako lahko za vas izboljЕЎamo spletno stran.","pc_trck_text_4":"Podatki, ki jih zbirajo ti piЕЎkotki, ne identificirajo nobenega posameznega uporabnika.","pc_trgt_text_1":"Ciljni in oglaЕЎevalski piЕЎkotki (ang. targeting and advertising cookies)","pc_trgt_text_2":"Ti piЕЎkotki se uporabljajo za prikazovanje spletnih oglasov, ki vas bodo na podlagi vaЕЎih navad pri brskanju verjetno zanimali.","pc_trgt_text_3":"Ti piЕЎkotki, ki jih uporabljajo naЕЎi oglaЕЎevalski ponudniki oz. ponudniki vsebine, lahko zdruЕѕujejo podatke, ki so jih zbrali na naЕЎi spletni strani, z drugimi podatki, ki so jih zbrali neodvisno v povezavi z dejavnostmi vaЕЎega spletnega brskalnika na njihovi mreЕѕi spletnih mest.","pc_trgt_text_4":"ДЊe se odloДЌite izbrisati oz. onemogoДЌiti te ciljne in oglaЕЎevalske piЕЎkotke, boste ЕЎe vedno videvali oglase, vendar ti morda ne bodo relevantni za vas.","pc_yprivacy_text_1":"Cenimo vaЕЎo zasebnost","pc_yprivacy_text_2":"PiЕЎkotki so majhne besedilne datoteke, ki se shranijo na vaЕЎo napravo ob obisku spletne strani. PiЕЎkotke uporabljamo v veДЌ namenov, predvsem pa za izboljЕЎanje vaЕЎe spletne izkuЕЎnje na naЕЎi strani (na primer za shranjevanje podatkov ob vpisu v vaЕЎ raДЌun).","pc_yprivacy_text_3":"VaЕЎe nastavitve lahko spremenite in onemogoДЌite doloДЌenim vrstam piЕЎkotkov, da bi se shranili na vaЕЎo napravo med brskanjem po naЕЎi spletni strani. Poleg tega lahko odstranite katerekoli piЕЎkotke, ki so Еѕe shranjeni v vaЕЎi napravi, a upoЕЎtevajte, da vam bo po izbrisu piЕЎkotkov morda onemogoДЌeno uporabljati dele naЕЎe spletne strani.","pc_yprivacy_title":"VaЕЎa zasebnost","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Politika zasebnosti</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktywne","always_active":"Zawsze aktywne","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Nieaktywne","nb_agree":"Zgoda","nb_changep":"Zmiana ustawieЕ„","nb_ok":"OK","nb_reject":"Odmawiam","nb_text":"UЕјywamy plikГіw cookie i innych technologii Е›ledzenia, aby poprawiД‡ jakoЕ›Д‡ przeglД…dania naszej witryny, wyЕ›wietlaД‡ spersonalizowane treЕ›ci i reklamy, analizowaД‡ ruch w naszej witrynie i wiedzieД‡, skД…d pochodzД… nasi uЕјytkownicy.","nb_title":"UЕјywamy pliki cookie","pc_fnct_text_1":"Funkcjonalne","pc_fnct_text_2":"Te pliki cookie sЕ‚uЕјД… do bardziej spersonalizowanego korzystania z naszej strony internetowej i do zapamiД™tywania wyborГіw dokonywanych podczas korzystania z naszej strony internetowej.","pc_fnct_text_3":"Na przykЕ‚ad moЕјemy uЕјywaД‡ funkcjonalnych plikГіw cookie do zapamiД™tywania preferencji jД™zykowych lub zapamiД™tywania danych logowania.","pc_minfo_text_1":"WiД™cej informacji","pc_minfo_text_2":"W przypadku jakichkolwiek pytaЕ„ dotyczД…cych naszej polityki dotyczД…cej plikГіw cookie i Twoich wyborГіw, skontaktuj siД™ z nami.","pc_minfo_text_3":"Aby dowiedzieД‡ siД™ wiД™cej, odwiedЕє naszД… <a href=\'%s\' target=\'_blank\'>Polityka prywatnoЕ›ci</a>.","pc_save":"Zapisz ustawienia","pc_sncssr_text_1":"NiezbД™dne","pc_sncssr_text_2":"Te pliki cookie sД… niezbД™dne do Е›wiadczenia usЕ‚ug dostД™pnych za poЕ›rednictwem naszej strony internetowej i umoЕјliwienia korzystania z niektГіrych funkcji naszej strony internetowej.","pc_sncssr_text_3":"Bez tych plikГіw cookie nie moЕјemy zapewniД‡ usЕ‚ug na naszej stronie internetowej.","pc_title":"Centrum ustawieЕ„ cookie","pc_trck_text_1":"Ељledzenie i wydajnoЕ›Д‡","pc_trck_text_2":"Te pliki cookie sЕ‚uЕјД… do zbierania informacji w celu analizy ruchu na naszej stronie internetowej i sposobu, w jaki uЕјytkownicy korzystajД… z naszej strony internetowej.","pc_trck_text_3":"Na przykЕ‚ad te pliki cookie mogД… Е›ledziД‡ takie rzeczy, jak czas spД™dzony na stronie lub odwiedzane strony, co pomaga nam zrozumieД‡, w jaki sposГіb moЕјemy ulepszyД‡ naszД… witrynД™ internetowД….","pc_trck_text_4":"Informacje zebrane przez te pliki nie identyfikujД… Ејadnego konkretnego uЕјytkownika.","pc_trgt_text_1":"Targeting i reklama","pc_trgt_text_2":"Te pliki cookie sЕ‚uЕјД… do wyЕ›wietlania reklam, ktГіre mogД… CiД™ zainteresowaД‡ na podstawie Twoich zwyczajГіw przeglД…dania.","pc_trgt_text_3":"Pliki te tworzone przez naszych dostawcГіw treЕ›ci i/lub reklam, mogД… Е‚Д…czyД‡ informacje zebrane z naszej strony z innymi informacjami, ktГіre gromadzili niezaleЕјnie w zwiД…zku z dziaЕ‚aniami przeglД…darki internetowej w ich sieci witryn.","pc_trgt_text_4":"JeЕ›li zdecydujesz siД™ usunД…Д‡ lub wyЕ‚Д…czyД‡ te pliki cookie, reklamy nadal bД™dД… wyЕ›wietlane, ale mogД… one nie byД‡ odpowiednie dla Ciebie.","pc_yprivacy_text_1":"Twoja prywatnoЕ›Д‡ jest dla nas waЕјna","pc_yprivacy_text_2":"Pliki cookie to bardzo maЕ‚e pliki tekstowe, ktГіre sД… tworzone i przechowywane na komputerze uЕјytkownika podczas odwiedzania strony internetowej. UЕјywamy plikГіw cookie do rГіЕјnych celГіw, w tym do ulepszania obsЕ‚ugi online na naszej stronie internetowej (na przykЕ‚ad, aby zapamiД™taД‡ dane logowania do konta).","pc_yprivacy_text_3":"MoЕјesz zmieniД‡ swoje ustawienia i odrzuciД‡ niektГіre rodzaje plikГіw cookie, ktГіre majД… byД‡ przechowywane na twoim komputerze podczas przeglД…dania naszej strony. MoЕјesz rГіwnieЕј usunД…Д‡ wszystkie pliki cookie juЕј zapisane na komputerze, ale pamiД™taj, Ејe usuniД™cie plikГіw cookie moЕјe uniemoЕјliwiД‡ korzystanie z czД™Е›ci naszej strony internetowej.","pc_yprivacy_title":"Twoja prywatnoЕ›Д‡","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Polityka prywatnoЕ›ci</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktivno","always_active":"Uvek aktivno","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Neaktivno","nb_agree":"SlaЕѕem se","nb_changep":"Promeni moja podeЕЎavanja","nb_ok":"OK","nb_reject":"Odbijam","nb_text":"Mi koristimo kolaДЌiД‡e i ostale tehnologije za praД‡enje kako bismo unapredili vaЕЎu pretragu na naЕЎem veb sajtu, prikazali personalizovani sadrЕѕaj i ciljane reklame, analizirali posete na naЕЎem sajtu i razumeli odakle dolaze naЕЎi posetioci sajta.","nb_title":"Mi koristimo kolaДЌiД‡e","pc_fnct_text_1":"Funkcionalni kolaДЌiД‡i","pc_fnct_text_2":"Ovi kolaДЌiД‡i koriste se za pruЕѕanje personalizovanijeg iskustva na naЕЎem veb sajtu i za pamД‡enje izbora koje pravite kada koristite naЕЎ veb sajt.","pc_fnct_text_3":"Na primer, moЕѕemo da koristimo funkcionalne kolaДЌiД‡e da bismo zapamtili vaЕЎe jeziДЌke postavke ili vaЕЎe podatke za prijavu.","pc_minfo_text_1":"ViЕЎe informacija","pc_minfo_text_2":"Za bilo koja pitanja vezana za naЕЎu politiku o kolaДЌiД‡ma i vaЕЎim izborima, molimo vas kontaktirajte nas.","pc_minfo_text_3":"Da saznate viЕЎe, pogledajte naЕЎu <a href=\'%s\' target=\'_blank\'>Pravila o privatnosti</a>.","pc_save":"SaДЌuvaj moja podeЕЎavanja","pc_sncssr_text_1":"Obavezni kolaДЌiД‡i","pc_sncssr_text_2":"Ovi kolaДЌiД‡i su neophodni za pruЕѕanje usluga dostupnih putem naЕЎeg veb sajta i za omoguД‡avanje koriЕЎД‡enja odreД‘enih funkcija naЕЎeg veb sajta.","pc_sncssr_text_3":"Bez ovih kolaДЌiД‡a ne moЕѕemo vam pruЕѕiti odreД‘ene usluge na naЕЎem veb sajtu.","pc_title":"Centar za podeЕЎavanje kolaДЌiД‡a","pc_trck_text_1":"KolaДЌiД‡i za praД‡enje i performanse","pc_trck_text_2":"Ovi kolaДЌiД‡i koriste se za prikupljanje informacija za analizu saobraД‡aja na naЕЎem veb sajtu i kako posetioci koriste naЕЎ veb sajt.","pc_trck_text_3":"Na primer, ovi kolaДЌiД‡i mogu pratiti stvari poput vremena koliko provodite na veb stranici ili stranicama koje poseД‡ujete ЕЎto nam pomaЕѕe da shvatimo kako moЕѕemo da poboljЕЎamo naЕЎ veb sajt.","pc_trck_text_4":"Informacije prikupljene ovim kolaДЌiД‡ima za praД‡enje i performanse ne identifikuju nijednog pojedinaДЌnog posetioca.","pc_trgt_text_1":"KolaДЌiД‡i za ciljanje i oglaЕЎavanje","pc_trgt_text_2":"Ovi kolaДЌiД‡i koriste se za prikazivanje reklama koje Д‡e vas verovatno zanimati na osnovu vaЕЎih navika pregledanja.","pc_trgt_text_3":"Ovi kolaДЌiД‡i, opsluЕѕeni od strane naЕЎih dobavljaДЌa sadrЕѕaja i / ili oglaЕЎavanja, mogu kombinovati informacije koje su sakupili sa naЕЎeg veb sajta sa drugim informacijama koje su nezavisno prikupili u vezi sa aktivnostima vaЕЎeg veb pretraЕѕivaДЌa kroz mreЕѕu njihovih veb sajtova.","pc_trgt_text_4":"Ako odluДЌite da uklonite ili onemoguД‡ite ove ciljane ili reklamne kolaДЌiД‡e i dalje Д‡ete videti reklame, ali one moЕѕda neД‡e biti relevantne za vas.","pc_yprivacy_text_1":"VaЕЎa privatnost je vaЕѕna za nas","pc_yprivacy_text_2":"KolaДЌiД‡i su veoma mali tekstualni fajlovi koji su saДЌuvani na vaЕЎem raДЌunaru kada posetite veb sajt. Mi koristimo kolaДЌiД‡e za razliДЌite svrhe i kako bi unapredili vaЕЎe onlajn iskustvo na naЕЎem veb sajtu (na primer, kako bi zapamtili vaЕЎe pristupne podatke).","pc_yprivacy_text_3":"Vi moЕѕete promeniti vaЕЎa podeЕЎavanja i odbiti odreД‘enu vrstu kolaДЌiД‡a koji Д‡e biti saДЌuvani na vaЕЎem raДЌunaru dok pregledate naЕЎ veb sajt. TakoД‘e moЕѕete izbrisati bilo koje kolaДЌiД‡e koji su veД‡ saДЌuvani u vaЕЎem raДЌunaru, ali imajte na umu da brisanjem kolaДЌiД‡a moЕѕete onemoguД‡iti pristup nekim delovima naЕЎeg veb sajta.","pc_yprivacy_title":"VaЕЎa privatnost","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Pravila o privatnosti</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Д®jungta","always_active":"Visada ДЇjungta","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"IЕЎjungta","nb_agree":"Sutinku","nb_changep":"Keisti mano pasirinkimus","nb_ok":"Gerai","nb_reject":"AЕЎ atsisakau","nb_text":"Mes naudojame slapukus ir kitas stebД—jimo technologijas, siekdami pagerinti jЕ«sЕі narЕЎymo mЕ«sЕі svetainД—je patirtДЇ, parodyti jums pritaikytД… turinДЇ ir tikslinius skelbimus, iЕЎanalizuoti mЕ«sЕі svetainД—s srautД… ir suprasti, iЕЎ kur ateina mЕ«sЕі lankytojai.","nb_title":"Mes naudojame slapukus","pc_fnct_text_1":"Funkcionalumo slapukai","pc_fnct_text_2":"Е ie slapukai naudojami siekiant suteikti jums asmeniЕЎkesnД™ patirtДЇ mЕ«sЕі svetainД—je ir prisiminti pasirinkimus, kuriuos atlikote, kai naudojatД—s mЕ«sЕі svetaine.","pc_fnct_text_3":"Pvz., Mes galime naudoti funkcinius slapukus, kad prisimintume jЕ«sЕі kalbos nustatymus arba prisimintume jЕ«sЕі prisijungimo duomenis.","pc_minfo_text_1":"Daugiau informacijos","pc_minfo_text_2":"DД—l bet kokiЕі klausimЕі, susijusiЕі su mЕ«sЕі slapukЕі politika ir jЕ«sЕі pasirinkimais, susisiekite su mumis.","pc_minfo_text_3":"NorД—dami suЕѕinoti daugiau, susipaЕѕinkite su mЕ«sЕі <a href=\'%s\' target=\'_blank\'>Privatumo politika</a>.","pc_save":"IЕЎsaugoti mano pasirinkimus","pc_sncssr_text_1":"Privalomi slapukai","pc_sncssr_text_2":"Е ie slapukai yra bЕ«tini norint suteikti jums paslaugas, pasiekiamas mЕ«sЕі svetainД—je, ir leisti naudotis tam tikromis mЕ«sЕі svetainД—s funkcijomis.","pc_sncssr_text_3":"Be ЕЎiЕі slapukЕі mes negalime jums suteikti tam tikrЕі paslaugЕі mЕ«sЕі svetainД—je.","pc_title":"SlapukЕі Pasirinkimo Centras","pc_trck_text_1":"StebД—jimo ir naЕЎumo slapukai","pc_trck_text_2":"Е ie slapukai naudojami rinkti informacijД…, siekiant analizuoti srautД… ДЇ mЕ«sЕі svetainД™ ir tai, kaip lankytojai naudojasi mЕ«sЕі svetaine.","pc_trck_text_3":"PavyzdЕѕiui, ЕЎie slapukai gali sekti kiek laiko praleidЕѕiate svetainД—je ar lankomuose puslapiuose, o tai padeda mums suprasti, kaip galime patobulinti savo svetainД™.","pc_trck_text_4":"Informacija, surinkta naudojant ЕЎiuos stebД—jimo ir naЕЎumo slapukus, neatpaЕѕДЇsta konkretaus lankytojo.","pc_trgt_text_1":"Tiksliniai ir reklaminiai slapukai","pc_trgt_text_2":"Е ie slapukai naudojami rodyti reklamД…, kuri greiДЌiausiai jus domina, atsiЕѕvelgiant ДЇ jЕ«sЕі narЕЎymo ДЇproДЌius.","pc_trgt_text_3":"Е ie slapukai, kuriuos teikia mЕ«sЕі turinio ir (arba) reklamos teikД—jai, gali apjungti informacijД…, kuriД… jie surinko iЕЎ mЕ«sЕі svetainД—s, su kita informacija, kuriД… jie rinko nepriklausomai, apie jЕ«sЕі interneto narЕЎyklД—s veiklД… jЕі svetainiЕі tinkle.","pc_trgt_text_4":"Jei nusprД™site paЕЎalinti arba iЕЎjungti ЕЎiuos tikslinius ar reklamavimo slapukus, vis tiek pamatysite skelbimus, taДЌiau jie gali bЕ«ti jums neaktualЕ«s.","pc_yprivacy_text_1":"Mums rЕ«pi jЕ«sЕі privatumas","pc_yprivacy_text_2":"Slapukai yra labai maЕѕi tekstiniai failai, kurie saugomi jЕ«sЕі kompiuteryje, kai apsilankote svetainД—je. Mes naudojame slapukus ДЇvairiais tikslais ir siekdami pagerinti jЕ«sЕі internetinД™ patirtДЇ mЕ«sЕі svetainД—je (pavyzdЕѕiui, jei norite, kad bЕ«tu ДЇsimenami jЕ«sЕі prisijungimo duomenys).","pc_yprivacy_text_3":"NarЕЎydami mЕ«sЕі svetainД—je galite pakeisti savo nustatymus ir atsisakyti tam tikrЕі tipЕі slapukЕі, kurie bus saugomi jЕ«sЕі kompiuteryje. Taip pat galite paЕЎalinti visus slapukus, jau saugomus jЕ«sЕі kompiuteryje, taДЌiau nepamirЕЎkite, kad iЕЎtrynД™ slapukus galite nepilnai naudotis mЕ«sЕі svetaine.","pc_yprivacy_title":"JЕ«sЕі privatumas","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Privatumo politika</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"AktД«vs","always_active":"VienmД“r aktД«vs","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"NeaktД«vs","nb_agree":"Es piekrД«tu","nb_changep":"MainД«t manas preferences","nb_ok":"OK","nb_reject":"Es noraidu","nb_text":"MД“s izmantojam sД«kdatnes un citas izsekoЕЎanas tehnoloДЈijas, lai uzlabotu JЕ«su pДЃrlЕ«koЕЎanas pieredzi mЕ«su vietnД“, parДЃdД«tu Jums personalizД“tu saturu un mД“rД·Д“tas reklДЃmas, analizД“tu mЕ«su vietnes datplЕ«smu un saprastu, no kurienes nДЃk mЕ«su apmeklД“tДЃji.","nb_title":"MД“s izmantojam sД«kdatnes","pc_fnct_text_1":"FunkcionalitДЃtes sД«kdatnes","pc_fnct_text_2":"Е Д«s sД«kdatnes tiek izmantotas, lai JЕ«s nodroЕЎinДЃtu ar personalizД“tu pieredzi mЕ«su mДЃjaslapДЃ un lai atcerД“tos izvД“les, kuras veicat izmantojot mЕ«su mДЃjaslapu.","pc_fnct_text_3":"PiemД“ram, mД“s varam izmantot funkcionalitДЃtes sД«kdatnes, lai atcerД“tos JЕ«su valodas preferences vai konta pieteikЕЎanДЃs datus.","pc_minfo_text_1":"VairДЃk informДЃcijas","pc_minfo_text_2":"Par jautДЃjumiem saistД«tiem ar mЕ«su sД«kdatЕ†u politiku un JЕ«su izvД“lД“m, lЕ«dzu, sazinieties ar mums.","pc_minfo_text_3":"Lai uzzinДЃtu vairДЃk, lЕ«dzu apmeklД“jiet mЕ«su <a href=\'%s\' target=\'_blank\'>Privacy Policy</a>.","pc_save":"SaglabДЃt manas preferences","pc_sncssr_text_1":"Strikti nepiecieЕЎamДЃs sД«kdatnes","pc_sncssr_text_2":"Е Д«s sД«kdatnes ir nepiecieЕЎamas, lai nodroЕЎinДЃtu Jums pakalpojumus, kas pieejami caur mЕ«su mДЃjaslapu un Дјautu Jums izmantot noteiktas mЕ«su vietnes funkcijas.","pc_sncssr_text_3":"Bez ЕЎД«m sД«kdatnД“m, mД“s nevaram Jums nodroЕЎinДЃt noteiktus pakalpojumus mЕ«su mДЃjaslapДЃ.","pc_title":"SД«kdatЕ†u PreferenДЌu Centrs","pc_trck_text_1":"IzsekoЕЎanas sД«kdatnes","pc_trck_text_2":"Е Д«s sД«kdatnes tiek izmantotas informДЃcijas apkopoЕЎanai, lai analizД“tu mЕ«su mДЃjaslapas datplЕ«smu, un kДЃ apmeklД“tДЃji izmanto mЕ«su mДЃjaslapu.","pc_trck_text_3":"PiemД“ram, ЕЎД«s sД«kdatnes var izsekot cik daudz laika JЕ«s pavadДЃt mДЃjaslapДЃ vai JЕ«su apmeklД“tДЃs lapas, kas mums palД«dz saprast, kДЃ mД“s Jums varam uzlabot mЕ«su mДЃjaslapu.","pc_trck_text_4":"InformДЃcija, kas savДЃkta, izmantojot ЕЎД«s izsekoЕЎanas un veiktspД“jas sД«kdatnes, neidentificД“ nevienu atseviЕЎД·u apmeklД“tДЃju.","pc_trgt_text_1":"MД“rД·auditorijas atlases un reklДЃmas sД«kdatnes","pc_trgt_text_2":"Е Д«s sД«kdatnes tiek izmantotas, lai rДЃdД«tu reklДЃmas, kas iespД“jams, JЕ«s interesД“s, pamatojoties uz JЕ«su pДЃrlЕ«koЕЎanas paradumiem.","pc_trgt_text_3":"Е Д«s sД«kdatnes, ko apkalpo mЕ«su satura un/vai reklДЃmas nodroЕЎinДЃtДЃji, var apvienot informДЃciju , kas savДЃkta no mЕ«su mДЃjaslapas ar citu viЕ†u rД«cД«bДЃ esoЕЎo informДЃciju, ko viЕ†i ir neatkarД«gi apkopojuЕЎi, kas saistД«ta ar JЕ«su tД«mekДјa pДЃrlЕ«kprogrammas darbД«bu viЕ†u vietЕ†u tД«klДЃ.","pc_trgt_text_4":"Ja JЕ«s izvД“laties noЕ†emt vai atspД“jot ЕЎД«s mД“rД·auditorijas atlases vai reklДЃmas sД«kdatnes, JЕ«s joprojДЃm redzД“siet reklДЃmas, bet tДЃs var nebЕ«t Jums aktuДЃlas.","pc_yprivacy_text_1":"Mums ir svarД«gs JЕ«su privДЃtums","pc_yprivacy_text_2":"SД«kdatnes ir Дјoti mazi teksta faili, kas tiek saglabДЃti JЕ«su datorДЃ, kad apmeklД“jat mДЃjaslapu. MД“s izmantojam sД«kdatnes daЕѕДЃdiem mД“rД·iem, un lai uzlabotu JЕ«su tieЕЎsaistes pieredzi mЕ«su mДЃjaslapДЃ (piemД“ram, lai atcerД“tos JЕ«su konta pieteikЕЎanДЃs datus).","pc_yprivacy_text_3":"JЕ«s varat mainД«t savas preferences un noraidД«t noteiktus sД«kfailu veidus, kas saglabДЃtos JЕ«su datorДЃ, pДЃrlЕ«kojot mЕ«su mДЃjaslapu. JЕ«s varat arД« noЕ†emt sД«kfailus, kas jau ir saglabДЃti JЕ«su datorДЃ, taДЌu paturiet prДЃtДЃ, ka sД«kdatЕ†u dzД“ЕЎana var liegt Jums izmantot atseviЕЎД·as daДјas no mЕ«su mДЃjaslapas.","pc_yprivacy_title":"JЕ«su privДЃtums","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Privacy Policy</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"РђРєС‚РёРІРЅРѕ","always_active":"Р’СЃРµРіРґР° Р°РєС‚РёРІРЅРѕ","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"РќРµР°РєС‚РёРІРЅРѕ","nb_agree":"РЇ СЃРѕРіР»Р°СЃРµРЅ","nb_changep":"РР·РјРµРЅРёС‚СЊ РјРѕРё РїСЂРµРґРїРѕС‡С‚РµРЅРёСЏ","nb_ok":"РћРє","nb_reject":"РЇ РѕС‚РєР°Р·С‹РІР°СЋСЃСЊ","nb_text":"РњС‹ РёСЃРїРѕР»СЊР·СѓРµРј С„Р°Р№Р»С‹ РєСѓРєРё Рё РґСЂСѓРіРёРµ С‚РµС…РЅРѕР»РѕРіРёРё РѕС‚СЃР»РµР¶РёРІР°РЅРёСЏ РґР»СЏ СѓР»СѓС‡С€РµРЅРёСЏ РІР°С€РµРіРѕ РїСЂРѕСЃРјРѕС‚СЂР° РЅР° РЅР°С€РµРј РІРµР±-СЃР°Р№С‚Рµ, С‡С‚РѕР±С‹ РїРѕРєР°Р·С‹РІР°С‚СЊ РІР°Рј РїРµСЂСЃРѕРЅР°Р»РёР·РёСЂРѕРІР°РЅРЅС‹Р№ РєРѕРЅС‚РµРЅС‚ Рё С‚Р°СЂРіРµС‚РёСЂРѕРІР°РЅРЅСѓСЋ СЂРµРєР»Р°РјСѓ, Р°РЅР°Р»РёР·РёСЂРѕРІР°С‚СЊ С‚СЂР°С„РёРє РЅР°С€РµРіРѕ РІРµР±-СЃР°Р№С‚Р° Рё РїРѕРЅРёРјР°С‚СЊ, РѕС‚РєСѓРґР° РїСЂРёС…РѕРґСЏС‚ РЅР°С€Рё РїРѕСЃРµС‚РёС‚РµР»Рё.","nb_title":"РњС‹ РёСЃРїРѕР»СЊР·СѓРµРј РєСѓРєРё","pc_fnct_text_1":"Р¤СѓРЅРєС†РёРѕРЅР°Р»СЊРЅС‹Рµ РєСѓРєРё","pc_fnct_text_2":"Р¤Р°Р№Р»С‹ РєСѓРєРё РёСЃРїРѕР»СЊР·СѓСЋС‚СЃСЏ, С‡С‚РѕР±С‹ РїСЂРµРґРѕСЃС‚Р°РІРёС‚СЊ РІР°Рј Р±РѕР»РµРµ РїРµСЂСЃРѕРЅР°Р»РёР·РёСЂРѕРІР°РЅРЅС‹Р№ РѕРїС‹С‚ РЅР° РЅР°С€РµРј РІРµР±-СЃР°Р№С‚Рµ Рё Р·Р°РїРѕРјРЅРёС‚СЊ РІС‹Р±РѕСЂ, РєРѕС‚РѕСЂС‹Р№ РІС‹ РґРµР»Р°РµС‚Рµ РїСЂРё РёСЃРїРѕР»СЊР·РѕРІР°РЅРёРё РЅР°С€РµРіРѕ РІРµР±-СЃР°Р№С‚Р°.","pc_fnct_text_3":"РќР°РїСЂРёРјРµСЂ, РјС‹ РјРѕР¶РµРј РёСЃРїРѕР»СЊР·РѕРІР°С‚СЊ С„СѓРЅРєС†РёРѕРЅР°Р»СЊРЅС‹Рµ С„Р°Р№Р»С‹ РєСѓРєРё, С‡С‚РѕР±С‹ Р·Р°РїРѕРјРЅРёС‚СЊ РІР°С€Рё СЏР·С‹РєРѕРІС‹Рµ РїСЂРµРґРїРѕС‡С‚РµРЅРёСЏ РёР»Рё РґР°РЅРЅС‹Рµ РґР»СЏ РІС…РѕРґР°.","pc_minfo_text_1":"Р‘РѕР»СЊС€Рµ РёРЅС„РѕСЂРјР°С†РёРё.","pc_minfo_text_2":"РџРѕ Р»СЋР±С‹Рј РІРѕРїСЂРѕСЃР°Рј, РєР°СЃР°СЋС‰РёРјСЃСЏ РЅР°С€РµР№ РїРѕР»РёС‚РёРєРё РІ РѕС‚РЅРѕС€РµРЅРёРё С„Р°Р№Р»РѕРІ РєСѓРєРё Рё РІР°С€РµРіРѕ РІС‹Р±РѕСЂР°, СЃРІСЏР¶РёС‚РµСЃСЊ СЃ РЅР°РјРё.","pc_minfo_text_3":"Р§С‚РѕР±С‹ СѓР·РЅР°С‚СЊ Р±РѕР»СЊС€Рµ, РїРѕСЃРµС‚РёС‚Рµ РЅР°С€ СЃР°Р№С‚ <a href=\'%s\' target=\'_blank\'>Privacy Policy</a>.","pc_save":"РЎРѕС…СЂР°РЅРёС‚СЊ РјРѕРё РїСЂРµРґРїРѕС‡С‚РµРЅРёСЏ","pc_sncssr_text_1":"РќРµРѕР±С…РѕРґРёРјС‹Рµ РєСѓРєРё","pc_sncssr_text_2":"Р¤Р°Р№Р»С‹ РєСѓРєРё РЅРµРѕР±С…РѕРґРёРјС‹ РґР»СЏ РїСЂРµРґРѕСЃС‚Р°РІР»РµРЅРёСЏ РІР°Рј СѓСЃР»СѓРі, РґРѕСЃС‚СѓРїРЅС‹С… С‡РµСЂРµР· РЅР°С€ РІРµР±-СЃР°Р№С‚, Рё РґР»СЏ С‚РѕРіРѕ, С‡С‚РѕР±С‹ РІС‹ РјРѕРіР»Рё РёСЃРїРѕР»СЊР·РѕРІР°С‚СЊ РѕРїСЂРµРґРµР»РµРЅРЅС‹Рµ С„СѓРЅРєС†РёРё РЅР°С€РµРіРѕ РІРµР±-СЃР°Р№С‚Р°.","pc_sncssr_text_3":"Р‘РµР· СЌС‚РёС… С„Р°Р№Р»РѕРІ РєСѓРєРё РјС‹ РЅРµ РјРѕР¶РµРј РїСЂРµРґРѕСЃС‚Р°РІР»СЏС‚СЊ РІР°Рј РѕРїСЂРµРґРµР»РµРЅРЅС‹Рµ С„СѓРЅРєС†РёРё РЅР° РЅР°С€РµРј РІРµР±-СЃР°Р№С‚Рµ.","pc_title":"Р¦РµРЅС‚СЂ РЅР°СЃС‚СЂРѕРµРє С„Р°Р№Р»РѕРІ РєСѓРєРё","pc_trck_text_1":"РћС‚СЃР»РµР¶РёРІР°РЅРёРµ РєСѓРєРё","pc_trck_text_2":"Р¤Р°Р№Р»С‹ РєСѓРєРё РёСЃРїРѕР»СЊР·СѓСЋС‚СЃСЏ РґР»СЏ СЃР±РѕСЂР° РёРЅС„РѕСЂРјР°С†РёРё РґР»СЏ Р°РЅР°Р»РёР·Р° С‚СЂР°С„РёРєР° РЅР° РЅР°С€ РІРµР±-СЃР°Р№С‚ Рё С‚РѕРіРѕ, РєР°Рє РїРѕСЃРµС‚РёС‚РµР»Рё РёСЃРїРѕР»СЊР·СѓСЋС‚ РЅР°С€ РІРµР±-СЃР°Р№С‚.","pc_trck_text_3":"РќР°РїСЂРёРјРµСЂ, СЌС‚Рё С„Р°Р№Р»С‹ РєСѓРєРё РјРѕРіСѓС‚ РѕС‚СЃР»РµР¶РёРІР°С‚СЊ С‚Р°РєРёРµ РІРµС‰Рё, РєР°Рє РІСЂРµРјСЏ, РєРѕС‚РѕСЂРѕРµ РІС‹ РїСЂРѕРІРѕРґРёС‚Рµ РЅР° РІРµР±-СЃР°Р№С‚Рµ РёР»Рё РїРѕСЃРµС‰Р°РµРјС‹Рµ РІР°РјРё СЃС‚СЂР°РЅРёС†С‹, С‡С‚Рѕ РїРѕРјРѕРіР°РµС‚ РЅР°Рј РїРѕРЅСЏС‚СЊ, РєР°Рє РјС‹ РјРѕР¶РµРј СѓР»СѓС‡С€РёС‚СЊ РЅР°С€ РІРµР±-СЃР°Р№С‚ РґР»СЏ РІР°СЃ.","pc_trck_text_4":"РРЅС„РѕСЂРјР°С†РёСЏ, СЃРѕР±СЂР°РЅРЅР°СЏ СЃ РїРѕРјРѕС‰СЊСЋ С„Р°Р№Р»РѕРІ РєСѓРєРё РґР»СЏ РѕС‚СЃР»РµР¶РёРІР°РЅРёСЏ Рё РїСЂРѕРёР·РІРѕРґРёС‚РµР»СЊРЅРѕСЃС‚Рё, РЅРµ РёРґРµРЅС‚РёС„РёС†РёСЂСѓРµС‚ РѕС‚РґРµР»СЊРЅРѕРіРѕ РїРѕСЃРµС‚РёС‚РµР»СЏ.","pc_trgt_text_1":"Р¦РµР»РµРІС‹Рµ Рё СЂРµРєР»Р°РјРЅС‹Рµ С„Р°Р№Р»С‹ РєСѓРєРё","pc_trgt_text_2":"Р­С‚Рё С„Р°Р№Р»С‹ РєСѓРєРё РёСЃРїРѕР»СЊР·СѓСЋС‚СЃСЏ РґР»СЏ РїРѕРєР°Р·Р° СЂРµРєР»Р°РјС‹, РєРѕС‚РѕСЂР°СЏ РјРѕР¶РµС‚ Р±С‹С‚СЊ РІР°Рј РёРЅС‚РµСЂРµСЃРЅР° РІ Р·Р°РІРёСЃРёРјРѕСЃС‚Рё РѕС‚ РІР°С€РёС… РїСЂРёРІС‹С‡РµРє РїСЂРѕСЃРјРѕС‚СЂР°.","pc_trgt_text_3":"Р­С‚Рё С„Р°Р№Р»С‹ РєСѓРєРё, РѕР±СЃР»СѓР¶РёРІР°РµРјС‹Рµ РЅР°С€РёРјРё РїРѕСЃС‚Р°РІС‰РёРєР°РјРё РєРѕРЅС‚РµРЅС‚Р° Рё / РёР»Рё СЂРµРєР»Р°РјС‹, РјРѕРіСѓС‚ РѕР±СЉРµРґРёРЅСЏС‚СЊ РёРЅС„РѕСЂРјР°С†РёСЋ, СЃРѕР±СЂР°РЅРЅСѓСЋ РёРјРё СЃ РЅР°С€РµРіРѕ РІРµР±-СЃР°Р№С‚Р°, СЃ РґСЂСѓРіРѕР№ РёРЅС„РѕСЂРјР°С†РёРµР№, РєРѕС‚РѕСЂСѓСЋ РѕРЅРё РЅРµР·Р°РІРёСЃРёРјРѕ СЃРѕР±РёСЂР°Р»Рё РѕС‚РЅРѕСЃРёС‚РµР»СЊРЅРѕ РґРµР№СЃС‚РІРёР№ РІР°С€РµРіРѕ Р±СЂР°СѓР·РµСЂР° РІ РёС… СЃРµС‚Рё РІРµР±-СЃР°Р№С‚РѕРІ.","pc_trgt_text_4":"Р•СЃР»Рё РІС‹ СЂРµС€РёС‚Рµ СѓРґР°Р»РёС‚СЊ РёР»Рё РѕС‚РєР»СЋС‡РёС‚СЊ СЌС‚Рё С†РµР»РµРІС‹Рµ РёР»Рё СЂРµРєР»Р°РјРЅС‹Рµ С„Р°Р№Р»С‹ РєСѓРєРё, РІС‹ РІСЃРµ СЂР°РІРЅРѕ Р±СѓРґРµС‚Рµ РІРёРґРµС‚СЊ СЂРµРєР»Р°РјСѓ, РЅРѕ РѕРЅР° РјРѕР¶РµС‚ РЅРµ РёРјРµС‚СЊ РѕС‚РЅРѕС€РµРЅРёСЏ Рє РІР°Рј.","pc_yprivacy_text_1":"Р’Р°С€Р° РєРѕРЅС„РёРґРµРЅС†РёР°Р»СЊРЅРѕСЃС‚СЊ РІР°Р¶РЅР° РґР»СЏ РЅР°СЃ","pc_yprivacy_text_2":"РљСѓРєРё - СЌС‚Рѕ РЅРµР±РѕР»СЊС€РёРµ С‚РµРєСЃС‚РѕРІС‹Рµ С„Р°Р№Р»С‹, РєРѕС‚РѕСЂС‹Рµ СЃРѕС…СЂР°РЅСЏСЋС‚СЃСЏ РЅР° РІР°С€РµРј РєРѕРјРїСЊСЋС‚РµСЂРµ, РєРѕРіРґР° Р’С‹ РїРѕСЃРµС‰Р°РµС‚Рµ РІРµР±-СЃР°Р№С‚. РњС‹ РёСЃРїРѕР»СЊР·СѓРµРј РєСѓРєРё РґР»СЏ СЂР°Р·Р»РёС‡РЅС‹С… С†РµР»РµР№, РІ С‚РѕРј С‡РёСЃР»Рµ РґР»СЏ С‚РѕРіРѕ, С‡С‚РѕР±С‹ СѓР»СѓС‡С€РёС‚СЊ РІР°С€Рµ РїСЂРµР±С‹РІР°РЅРёРµ РЅР° РЅР°С€РµРј РІРµР±-СЃР°Р№С‚Рµ (РЅР°РїСЂРёРјРµСЂ, С‡С‚РѕР±С‹ Р·Р°РїРѕРјРЅРёС‚СЊ РґР°РЅРЅС‹Рµ РґР»СЏ РІС…РѕРґР° РІ РІР°С€Сѓ СѓС‡РµС‚РЅСѓСЋ Р·Р°РїРёСЃСЊ).","pc_yprivacy_text_3":"Р’С‹ РјРѕР¶РµС‚Рµ РёР·РјРµРЅРёС‚СЊ СЃРІРѕРё РїСЂРµРґРїРѕС‡С‚РµРЅРёСЏ Рё РѕС‚РєР°Р·Р°С‚СЊСЃСЏ РѕС‚ СЃРѕС…СЂР°РЅРµРЅРёСЏ РѕРїСЂРµРґРµР»РµРЅРЅС‹С… С‚РёРїРѕРІ С„Р°Р№Р»РѕРІ cookie РЅР° РІР°С€РµРј РєРѕРјРїСЊСЋС‚РµСЂРµ РІРѕ РІСЂРµРјСЏ РїСЂРѕСЃРјРѕС‚СЂР° РЅР°С€РµРіРѕ РІРµР±-СЃР°Р№С‚Р°. Р’С‹ С‚Р°РєР¶Рµ РјРѕР¶РµС‚Рµ СѓРґР°Р»РёС‚СЊ Р»СЋР±С‹Рµ С„Р°Р№Р»С‹ РєСѓРєРё, СѓР¶Рµ С…СЂР°РЅСЏС‰РёРµСЃСЏ РЅР° РІР°С€РµРј РєРѕРјРїСЊСЋС‚РµСЂРµ, РЅРѕ РёРјРµР№С‚Рµ РІ РІРёРґСѓ, С‡С‚Рѕ СѓРґР°Р»РµРЅРёРµ С„Р°Р№Р»РѕРІ cookie РјРѕР¶РµС‚ РїРѕРјРµС€Р°С‚СЊ РІР°Рј РёСЃРїРѕР»СЊР·РѕРІР°С‚СЊ РЅРµРєРѕС‚РѕСЂС‹Рµ С‡Р°СЃС‚Рё РЅР°С€РµРіРѕ РІРµР±-СЃР°Р№С‚Р°.","pc_yprivacy_title":"Р’Р°С€Р° РєРѕРЅС„РёРґРµРЅС†РёР°Р»СЊРЅРѕСЃС‚СЊ","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Privacy Policy</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Aktiv","always_active":"Alltid aktiv","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Inaktiv","nb_agree":"Godta alle","nb_changep":"Endre innstillinger","nb_ok":"OK","nb_reject":"Avvis alle","nb_text":"Vi bruker informasjonskapsler og andre sporingsteknologier for ГҐ forbedre din nettleseropplevelse pГҐ nettstedet vГҐrt, for ГҐ vise deg personlig tilpasset innhold og mГҐlrettede annonser, for ГҐ analysere nettstedstrafikken vГҐr og for ГҐ forstГҐ hvor vГҐre besГёkende kommer fra.","nb_title":"Vi bruker informasjonskapsler","pc_fnct_text_1":"Funksjonalitetscookies","pc_fnct_text_2":"Disse informasjonskapslene brukes til ГҐ gi deg en mer personlig opplevelse pГҐ nettstedet vГҐrt og til ГҐ huske valg du tar nГҐr du bruker nettstedet vГҐrt.","pc_fnct_text_3":"For eksempel kan vi bruke funksjonalitetscookies for ГҐ huske sprГҐkinnstillingene dine eller huske pГҐloggingsinformasjonen din.","pc_minfo_text_1":"Mer informasjon","pc_minfo_text_2":"For spГёrsmГҐl angГҐende vГҐre retningslinjer for informasjonskapsler og dine valg, vennligst kontakt oss.","pc_minfo_text_3":"For ГҐ finne ut mer, besГёk vГҐr <a href=\'%s\' target=\'_blank\'>personvernpolicy</a>.","pc_save":"Lagre mine preferanser","pc_sncssr_text_1":"Strengt nГёdvendige informasjonskapsler","pc_sncssr_text_2":"Disse informasjonskapslene er viktige for ГҐ gi deg tjenester tilgjengelig via nettstedet vГҐrt og for ГҐ gjГёre det mulig for deg ГҐ bruke visse funksjoner pГҐ nettstedet vГҐrt.","pc_sncssr_text_3":"Uten disse informasjonskapslene kan vi ikke tilby deg visse tjenester pГҐ nettstedet vГҐrt.","pc_title":"Informasjonssenter for informasjonskapsler","pc_trck_text_1":"Sporings- og ytelses-informasjonskapsler","pc_trck_text_2":"Disse informasjonskapslene brukes til ГҐ samle inn informasjon for ГҐ analysere trafikken til nettstedet vГҐrt og hvordan besГёkende bruker nettstedet vГҐrt","pc_trck_text_3":"Disse informasjonskapslene kan for eksempel spore ting som hvor lang tid du bruker pГҐ nettstedet eller sidene du besГёker, noe som hjelper oss ГҐ forstГҐ hvordan vi kan forbedre nettstedet vГҐrt for deg.","pc_trck_text_4":"Informasjonen som samles inn gjennom disse sporings- og ytelseskapslene, identifiserer ikke noen individuell besГёkende.","pc_trgt_text_1":"MГҐlretting og annonsering av informasjonskapsler","pc_trgt_text_2":"Disse informasjonskapslene brukes til ГҐ vise reklame som sannsynligvis vil vГ¦re av interesse for deg basert pГҐ nettleservaner.","pc_trgt_text_3":"Disse informasjonskapslene, som serveres av innholds- og / eller reklameleverandГёrer, kan kombinere informasjon de har samlet inn fra nettstedet vГҐrt med annen informasjon de har samlet uavhengig av nettleserens aktiviteter pГҐ tvers av nettverket av nettsteder.","pc_trgt_text_4":"Hvis du velger ГҐ fjerne eller deaktivere disse mГҐlrettings- eller annonseringskapslene, vil du fremdeles se annonser, men de er kanskje ikke relevante for deg.","pc_yprivacy_text_1":"Ditt personvern er viktig for oss","pc_yprivacy_text_2":"Informasjonskapsler er veldig smГҐ tekstfiler som lagres pГҐ datamaskinen din nГҐr du besГёker et nettsted. Vi bruker informasjonskapsler for en rekke formГҐl og for ГҐ forbedre din online opplevelse pГҐ nettstedet vГҐrt (for eksempel for ГҐ huske pГҐloggingsinformasjonen din).","pc_yprivacy_text_3":"Du kan endre innstillingene dine og avvise visse typer informasjonskapsler som skal lagres pГҐ datamaskinen din mens du surfer pГҐ nettstedet vГҐrt. Du kan ogsГҐ fjerne alle informasjonskapsler som allerede er lagret pГҐ datamaskinen din, men husk at sletting av informasjonskapsler kan forhindre deg i ГҐ bruke deler av nettstedet vГҐrt.","pc_yprivacy_title":"Ditt personvern","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Personvernpolicy</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Р’ РґРµР№СЃС‚РІРёРµ СЃР° Р±РёСЃРєРІРёС‚РєРёС‚Рµ","always_active":"Р’РёРЅР°РіРё РІ РґРµР№СЃС‚РІРёРµ СЃР° Р±РёСЃРєРІРёС‚РєРёС‚Рµ","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"РќРµР°РєС‚РёРІРЅРё Р±РёСЃРєРІРёС‚РєРё","nb_agree":"РЎСЉРіР»Р°СЃРµРЅ СЃСЉРј","nb_changep":"РџСЂРѕРјСЏРЅР° РЅР° РїСЂРµРґРїРѕС‡РёС‚Р°РЅРёСЏС‚Р° РјРё","nb_ok":"Р”РѕР±СЂРµ","nb_reject":"РђР· РѕС‚РєР°Р·РІР°Рј","nb_text":"РќРёРµ РёР·РїРѕР»Р·РІР°РјРµ Р±РёСЃРєРІРёС‚РєРё Рё РґСЂСѓРіРё, РїСЂРѕСЃР»РµРґСЏРІР°С‰Рё, С‚РµС…РЅРѕР»РѕРіРёРё, Р·Р° РґР° РїРѕРґРѕР±СЂРёРј СЃСЉСЂС„РёСЂР°РЅРµС‚Рѕ РІРё РІ РЅР°С€РёСЏ СЃР°Р№С‚, РєР°С‚Рѕ РІРё РїРѕРєР°Р¶РµРј РїРµСЂСЃРѕРЅР°Р»РёР·РёСЂР°РЅРѕ СЃСЉРґСЉСЂР¶Р°РЅРёРµ Рё СЂРµРєР»Р°РјРё, РґР° Р°РЅР°Р»РёР·РёСЂР°РјРµ С‚СЂР°С„РёРєР° РЅР° РЅР°С€РёСЏ СЃР°Р№С‚ Рё РґР° СЂР°Р·Р±РµСЂРµРј РѕС‚РєСЉРґРµ РёРґРІР°С‚ РЅР°С€РёС‚Рµ РїРѕСЃРµС‚РёС‚РµР»Рё.","nb_title":"РќРёРµ РёР·РїРѕР»Р·РІР°РјРµ Р±РёСЃРєРІРёС‚РєРё","pc_fnct_text_1":"Р¤СѓРЅРєС†РёРѕРЅР°Р»РЅРё Р±РёСЃРєРІРёС‚РєРё","pc_fnct_text_2":"РўРµР·Рё Р±РёСЃРєРІРёС‚РєРё СЃРµ РёР·РїРѕР»Р·РІР°С‚, Р·Р° РґР° РІРё РѕСЃРёРіСѓСЂСЏС‚ РѕС‰Рµ РїРѕ-РїРµСЂСЃРѕРЅР°Р»РёР·РёСЂР°РЅРѕ РёР·Р¶РёРІСЏРІР°РЅРµ РЅР° РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚ Рё РґР° Р±СЉРґР°С‚ Р·Р°РїРѕРјРЅРµРЅРё РёР·Р±РѕСЂРёС‚Рµ, РєРѕРёС‚Рѕ СЃС‚Рµ РЅР°РїСЂР°РІРёР»Рё, РєРѕРіР°С‚Рѕ РёР·РїРѕР»Р·РІР°С…С‚Рµ РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚.","pc_fnct_text_3":"РќР°РїСЂРёРјРµСЂ: РјРѕР¶Рµ РґР° РёР·РїРѕР»Р·РІР°РјРµ С„СѓРЅРєС†РёРѕРЅР°Р»РЅРё Р±РёСЃРєРІРёС‚РєРё, Р·Р° РґР° Р·Р°РїРѕРјРЅРёРј РїСЂРµРґРїРѕС‡РёС‚Р°РЅРёСЏ РІРё РµР·РёРє РёР»Рё РґР° Р·Р°РїРѕРјРЅРёРј РґРµС‚Р°Р№Р»Рё РїРѕ РІР»РёР·Р°РЅРµС‚Рѕ РІРё РІ СѓРµР±СЃР°Р№С‚Р°.","pc_minfo_text_1":"РћС‰Рµ РёРЅС„РѕСЂРјР°С†РёСЏ","pc_minfo_text_2":"Р—Р° РІСЃСЏРєР°РєРІРё РІСЉРїСЂРѕСЃРё РІСЉРІ РІСЂСЉР·РєР° СЃ РЅР°С€Р°С‚Р° РїРѕР»РёС‚РёРєР° Р·Р° Р±РёСЃРєРІРёС‚РєРёС‚Рµ Рё РІР°С€РёС‚Рµ РёР·Р±РѕСЂРё, РјРѕР»СЏ, СЃРІСЉСЂР¶РµС‚Рµ СЃРµ СЃ РЅР°СЃ.","pc_minfo_text_3":"Р—Р° РґР° РЅР°СѓС‡РёС‚Рµ РїРѕРІРµС‡Рµ, РјРѕР»СЏ, РїРѕСЃРµС‚РµС‚Рµ РЅР°С€Р°С‚Р° <a href=\'%s\' target=\'_blank\'>РЎС‚СЂР°РЅРёС†Р° Р·Р° РїРѕРІРµСЂРёС‚РµР»РЅРѕСЃС‚</a>.","pc_save":"Р—Р°РїР°Р·Рё РїСЂРµРґРїРѕС‡РёС‚Р°РЅРёСЏС‚Р° РјРё","pc_sncssr_text_1":"РЎС‚СЂРѕРіРѕ Р·Р°РґСЉР»Р¶РёС‚РµР»РЅРё Р±РёСЃРєРІРёС‚РєРё","pc_sncssr_text_2":"РўРµР·Рё Р±РёСЃРєРІРёС‚РєРё СЃР° СЃСЉС‰РµСЃС‚РІРµРЅ РµР»РµРјРµРЅС‚, РєРѕР№С‚Рѕ РѕСЃРёРіСѓСЂСЏРІР° СѓСЃР»СѓРіРё, РґРѕСЃС‚СЉРїРЅРё С‡СЂРµР· РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚ Рё РґР°РІР°С‚ РІСЉР·РјРѕР¶РЅРѕСЃС‚ Р·Р° РёР·РїРѕР»Р·РІР°РЅРµ РЅР° РѕРїСЂРµРґРµР»РµРЅРё С„СѓРЅРєС†РёРё РЅР° РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚.","pc_sncssr_text_3":"Р‘РµР· С‚РµР·Рё Р±РёСЃРєРІРёС‚РєРё РЅРµ РјРѕР¶РµРј РґР° РІРё РґРѕСЃС‚Р°РІРёРј РѕРїСЂРµРґРµР»РµРЅРё СѓСЃР»СѓРіРё РЅР° РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚.","pc_title":"Р¦РµРЅС‚СЉСЂ Р·Р° РЅР°СЃС‚СЂРѕР№РєР° РЅР° Р±РёСЃРєРІРёС‚РєРё","pc_trck_text_1":"Р‘РёСЃРєРІРёС‚РєРё Р·Р° РїСЂРѕСЃР»РµРґСЏРІР°РЅРµ Рё Р·Р° РїСЂРѕРёР·РІРѕРґРёС‚РµР»РЅРѕСЃС‚","pc_trck_text_2":"РўРµР·Рё Р±РёСЃРєРІРёС‚РєРё СЃРµ РёР·РїРѕР»Р·РІР°С‚ Р·Р° СЃСЉР±РёСЂР°РЅРµ РЅР° РёРЅС„РѕСЂРјР°С†РёСЏ Р·Р° Р°РЅР°Р»РёР· РЅР° С‚СЂР°С„РёРєР° РєСЉРј РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚ Рё РєР°Рє РїРѕСЃРµС‚РёС‚РµР»РёС‚Рµ РёР·РїРѕР»Р·РІР°С‚ РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚.","pc_trck_text_3":"РќР°РїСЂРёРјРµСЂ, С‚РµР·Рё Р±РёСЃРєРІРёС‚РєРё РјРѕРіР°С‚ РґР° РїСЂРѕСЃР»РµРґСЏРІР°С‚ РЅРµС‰Р° РєР°С‚Рѕ РєРѕР»РєРѕ РІСЂРµРјРµ РїСЂРµРєР°СЂРІР°С‚Рµ РЅР° СѓРµР±СЃР°Р№С‚Р° РёР»Рё РЅР° РїРѕСЃРµС‰Р°РІР°РЅРёС‚Рµ РѕС‚ РІР°СЃ СЃС‚СЂР°РЅРёС†Рё, РєРѕРµС‚Рѕ РЅРё РїРѕРјР°РіР° РґР° СЂР°Р·Р±РµСЂРµРј РєР°Рє РјРѕР¶РµРј РґР° РїРѕРґРѕР±СЂРёРј РЅР°С€РёСЏ СЃР°Р№С‚ Р·Р° РІР°СЃ.","pc_trck_text_4":"РРЅС„РѕСЂРјР°С†РёСЏС‚Р°, СЃСЉР±СЂР°РЅР° С‡СЂРµР· С‚РµР·Рё Р±РёСЃРєРІРёС‚РєРё Р·Р° РїСЂРѕСЃР»РµРґСЏРІР°РЅРµ Рё РїСЂРѕРёР·РІРѕРґРёС‚РµР»РЅРѕСЃС‚, РЅРµ РёРґРµРЅС‚РёС„РёС†РёСЂР° РІСЃРµРєРё РѕС‚РґРµР»РµРЅ РїРѕСЃРµС‚РёС‚РµР».","pc_trgt_text_1":"РќР°СЃРѕС‡РІР°РЅРµ Рё СЂРµРєР»Р°РјРЅРё Р±РёСЃРєРІРёС‚РєРё","pc_trgt_text_2":"РўРµР·Рё Р±РёСЃРєРІРёС‚РєРё СЃРµ РёР·РїРѕР»Р·РІР°С‚ Р·Р° РїРѕРєР°Р·РІР°РЅРµ РЅР° СЂРµРєР»Р°РјР°, РєРѕСЏС‚Рѕ РІРµСЂРѕСЏС‚РЅРѕ С‰Рµ РІРё Р·Р°РёРЅС‚РµСЂРµСЃРѕРІР° РІСЉР· РѕСЃРЅРѕРІР° РЅР° РЅР°РІРёС†РёС‚Рµ РІРё Р·Р° СЃСЉСЂС„РёСЂР°РЅРµ.","pc_trgt_text_3":"РўРµР·Рё Р±РёСЃРєРІРёС‚РєРё, РѕР±СЃР»СѓР¶РІР°РЅРё РѕС‚ РЅР°С€РёС‚Рµ РґРѕСЃС‚Р°РІС‡РёС†Рё РЅР° СЃСЉРґСЉСЂР¶Р°РЅРёРµ Рё / РёР»Рё СЂРµРєР»Р°РјР°, РјРѕРіР°С‚ РґР° РєРѕРјР±РёРЅРёСЂР°С‚ РёРЅС„РѕСЂРјР°С†РёСЏС‚Р°, РєРѕСЏС‚Рѕ СЃР° СЃСЉР±СЂР°Р»Рё РѕС‚ РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚, СЃ РґСЂСѓРіР° РёРЅС„РѕСЂРјР°С†РёСЏ, РєРѕСЏС‚Рѕ СЃР° СЃСЉР±СЂР°Р»Рё РЅРµР·Р°РІРёСЃРёРјРѕ, СЃРІСЉСЂР·Р°РЅР° СЃ РґРµР№РЅРѕСЃС‚РёС‚Рµ РЅР° РІР°С€РёСЏ СѓРµР± Р±СЂР°СѓР·СЉСЂ РІ С‚СЏС…РЅР°С‚Р° РјСЂРµР¶Р° РѕС‚ СѓРµР±СЃР°Р№С‚РѕРІРµ.","pc_trgt_text_4":"РђРєРѕ СЂРµС€РёС‚Рµ РґР° РїСЂРµРјР°С…РЅРµС‚Рµ РёР»Рё РґРµР°РєС‚РёРІРёСЂР°С‚Рµ С‚РµР·Рё Р±РёСЃРєРІРёС‚РєРё Р·Р° РѕРїСЂРµРґРµР»РµРЅРё РїРѕС‚СЂРµР±РёС‚РµР»СЃРєРё РіСЂСѓРїРё РёР»Рё СЂРµРєР»Р°РјР°, РїР°Рє С‰Рµ РІРёРґРёС‚Рµ СЂРµРєР»Р°РјРё, РЅРѕ С‚Рµ РјРѕР¶Рµ РґР° РЅРµ СЃР° РѕС‚ РїРѕРґС…РѕРґСЏС‰Рё Р·Р° РІР°СЃ.","pc_yprivacy_text_1":"Р’Р°С€Р°С‚Р° РїРѕРІРµСЂРёС‚РµР»РЅРѕСЃС‚ Рµ РІР°Р¶РЅР° Р·Р° РЅР°СЃ","pc_yprivacy_text_2":"Р‘РёСЃРєРІРёС‚РєРёС‚Рµ СЃР° РјРЅРѕРіРѕ РјР°Р»РєРё С‚РµРєСЃС‚РѕРІРё С„Р°Р№Р»РѕРІРµ, РєРѕРёС‚Рѕ СЃРµ СЃСЉС…СЂР°РЅСЏРІР°С‚ РЅР° РІР°С€РёСЏ РєРѕРјРїСЋС‚СЉСЂ, РєРѕРіР°С‚Рѕ РїРѕСЃРµС‚РёС‚Рµ СѓРµР±СЃР°Р№С‚. РќРёРµ РёР·РїРѕР»Р·РІР°РјРµ Р±РёСЃРєРІРёС‚РєРё Р·Р° РјРЅРѕР¶РµСЃС‚РІРѕ РѕС‚ С†РµР»Рё Рё РґР° РїРѕРґРѕР±СЂРёРј СЃСЉСЂС„РёСЂР°РЅРµС‚Рѕ РІРё РёР· РЅР°С€РёСЏ СЃР°Р№С‚ (РЅР°РїСЂРёРјРµСЂ: Р·Р° РґР° Р·Р°РїРѕРјРЅРёРј РґРµС‚Р°Р№Р»РёС‚Рµ РЅР° РІР°С€РёСЏ Р°РєР°СѓРЅС‚ Р·Р° РІР»РёР·Р°РЅРµ).","pc_yprivacy_text_3":"РњРѕР¶РµС‚Рµ РґР° РїСЂРѕРјРµРЅРёС‚Рµ РїСЂРµРґРїРѕС‡РёС‚Р°РЅРёСЏС‚Р° СЃРё Рё РґР° РѕС‚РєР°Р¶РµС‚Рµ РѕРїСЂРµРґРµР»РµРЅРё РІРёРґРѕРІРµ Р±РёСЃРєРІРёС‚РєРё, РєРѕРёС‚Рѕ РґР° СЃРµ СЃСЉС…СЂР°РЅСЏРІР°С‚ РЅР° РІР°С€РёСЏ РєРѕРјРїСЋС‚СЉСЂ, РґРѕРєР°С‚Рѕ СЃСЉСЂС„РёСЂР°С‚Рµ РІ РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚. РњРѕР¶РµС‚Рµ СЃСЉС‰Рѕ РґР° РїСЂРµРјР°С…РЅРµС‚Рµ РЅСЏРєРѕРё Р±РёСЃРєРІРёС‚РєРё, РєРѕРёС‚Рѕ РІРµС‡Рµ СЃР° Р·Р°РїР°Р·РµРЅРё РЅР° РІР°С€РёСЏ РєРѕРјРїСЋС‚СЉСЂ, РЅРѕ РёРјР°Р№С‚Рµ РїСЂРµРґРІРёРґ, С‡Рµ РёР·С‚СЂРёРІР°РЅРµС‚Рѕ РЅР° Р±РёСЃРєРІРёС‚РєРё РјРѕР¶Рµ РґР° РІРё РїРѕРїСЂРµС‡Рё РґР° РёР·РїРѕР»Р·РІР°С‚Рµ С‡Р°СЃС‚Рё РѕС‚ РЅР°С€РёСЏ СѓРµР±СЃР°Р№С‚.","pc_yprivacy_title":"Р’Р°С€Р°С‚Р° РїРѕРІРµСЂРёС‚РµР»РЅРѕСЃС‚","privacy_policy":"<a href=\'%s\' target=\'_blank\'>РЎС‚СЂР°РЅРёС†Р° Р·Р° РїРѕРІРµСЂРёС‚РµР»РЅРѕСЃС‚</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"О•ОЅОµПЃОіПЊ","always_active":"О О¬ОЅП„О± ОµОЅОµПЃОіПЊ","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"О‘ОЅОµОЅОµПЃОіПЊ","nb_agree":"ОЈП…ОјП†П‰ОЅПЋ","nb_changep":"О‘О»О»О±ОіО® П„П‰ОЅ ПЂПЃОїП„О№ОјО®ПѓОµПЋОЅ ОјОїП…","nb_ok":"OK","nb_reject":"О‘ПЃОЅОїПЌОјО±О№","nb_text":"О§ПЃО·ПѓО№ОјОїПЂОїО№ОїПЌОјОµ cookies ОєО±О№ О¬О»О»ОµП‚ П„ОµП‡ОЅОїО»ОїОіОЇОµП‚ ОµОЅП„ОїПЂО№ПѓОјОїПЌ ОіО№О± П„О·ОЅ ОІОµО»П„ОЇП‰ПѓО· П„О·П‚ ОµОјПЂОµО№ПЃОЇО±П‚ ПЂОµПЃО№О®ОіО·ПѓО·П‚ ПѓП„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚, ОіО№О± П„О·ОЅ ОµОѕО±П„ОїОјОЇОєОµП…ПѓО· ПЂОµПЃО№ОµП‡ОїОјО­ОЅОїП… ОєО±О№ ОґО№О±П†О·ОјОЇПѓОµП‰ОЅ, П„О·ОЅ ПЂО±ПЃОїП‡О® О»ОµО№П„ОїП…ПЃОіО№ПЋОЅ ОєОїО№ОЅП‰ОЅО№ОєПЋОЅ ОјО­ПѓП‰ОЅ ОєО±О№ П„О·ОЅ О±ОЅО¬О»П…ПѓО· П„О·П‚ ОµПЂО№ПѓОєОµП€О№ОјПЊП„О·П„О¬П‚ ОјО±П‚.","nb_title":"О‘П…П„О® О· О№ПѓП„ОїПѓОµО»ОЇОґО± П‡ПЃО·ПѓО№ОјОїПЂОїО№ОµОЇ cookies","pc_fnct_text_1":"Cookies О›ОµО№П„ОїП…ПЃОіО№ОєПЊП„О·П„О±П‚","pc_fnct_text_2":"О‘П…П„О¬ П„О± cookies П‡ПЃО·ПѓО№ОјОїПЂОїО№ОїПЌОЅП„О±О№ ОіО№О± ОЅО± ПѓО±П‚ ПЂО±ПЃО­П‡ОїП…ОЅ ОјОЇО± ПЂО№Ої ПЂПЃОїПѓП‰ПЂОїПЂОїО№О·ОјО­ОЅО· ОµОјПЂОµО№ПЃОЇО± ПѓП„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚ ОєО±О№ ОіО№О± ОЅО± ОёП…ОјОїПЌОЅП„О±О№ ОµПЂО№О»ОїОіО­П‚ ПЂОїП… ОєО¬ОЅОµП„Оµ ПЊП„О±ОЅ П‡ПЃО·ПѓО№ОјОїПЂОїО№ОµОЇП„Оµ П„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚.","pc_fnct_text_3":"О“О№О± ПЂО±ПЃО¬ОґОµО№ОіОјО±, ОјПЂОїПЃОµОЇ ОЅО± П‡ПЃО·ПѓО№ОјОїПЂОїО№О®ПѓОїП…ОјОµ cookies О»ОµО№П„ОїП…ПЃОіО№ОєПЊП„О·П„О±П‚ ОіО№О± ОЅО± ОёП…ОјПЊОјО±ПѓП„Оµ П„О·ОЅ ОµПЂО№О»ОїОіО® ОіО»ПЋПѓПѓО±П‚ О® П„О± ПѓП„ОїО№П‡ОµОЇО± ОµО№ПѓПЊОґОїП… ПѓО±П‚.","pc_minfo_text_1":"О ОµПЃО№ПѓПѓПЊП„ОµПЃОµП‚ ПЂО»О·ПЃОїП†ОїПЃОЇОµП‚","pc_minfo_text_2":"О“О№О± ОїПЂОїО№О±ОґО®ПЂОїП„Оµ О±ПЂОїПЃОЇО± ПѓОµ ПѓП‡О­ПѓО· ОјОµ П„О·ОЅ ПЂОїО»О№П„О№ОєО® ОјО±П‚ ПѓП‡ОµП„О№ОєО¬ ОјОµ П„О± cookies ОєО±О№ П„О№П‚ ОµПЂО№О»ОїОіО­П‚ ПѓО±П‚, ПЂО±ПЃО±ОєО±О»ОїПЌОјОµ ОЅО± О­ПЃОёОµП„Оµ ПѓОµ ОµПЂО±П†О® ОјО±О¶ОЇ ОјО±П‚.","pc_minfo_text_3":"О“О№О± ОЅО± ОјО¬ОёОµП„Оµ ПЂОµПЃО№ПѓПѓПЊП„ОµПЃО±, ПЂО±ПЃО±ОєО±О»ОїПЌОјОµ ОµПЂО№ПѓОєОµП†ОёОµОЇП„Оµ П„О·ОЅ ПѓОµО»ОЇОґО± ПЂОµПЃОЇ <a href=\'%s\' target=\'_blank\'>О ОїО»О№П„О№ОєО® О±ПЂОїПЃПЃО®П„ОїП…</a>.","pc_save":"О‘ПЂОїОёО®ОєОµП…ПѓО· П„П‰ОЅ ПЂПЃОїП„О№ОјО®ПѓОµПЋОЅ ОјОїП…","pc_sncssr_text_1":"О†ОєПЃП‰П‚ О±ПЂО±ПЃО±ОЇП„О·П„О± cookies","pc_sncssr_text_2":"О¤О± О±ПЂО±ПЃО±ОЇП„О·П„О± cookies ОІОїО·ОёОїПЌОЅ ПѓП„Ої ОЅО± ОіОЇОЅОµО№ П‡ПЃО·ПѓП„О№ОєО® ОјОЇО± О№ПѓП„ОїПѓОµО»ОЇОґО±, ОµПЂО№П„ПЃО­ПЂОїОЅП„О±П‚ ОІО±ПѓО№ОєО­П‚ О»ОµО№П„ОїП…ПЃОіОЇОµП‚ ПЊПЂП‰П‚ П„О·ОЅ ПЂО»ОїО®ОіО·ПѓО· ОєО±О№ П„О·ОЅ ПЂПЃПЊПѓОІО±ПѓО· ПѓОµ О±ПѓП†О±О»ОµОЇП‚ ПЂОµПЃО№ОїП‡О­П‚ П„О·П‚ О№ПѓП„ОїПѓОµО»ОЇОґО±П‚.","pc_sncssr_text_3":"О— О№ПѓП„ОїПѓОµО»ОЇОґО± ОґОµОЅ ОјПЂОїПЃОµОЇ ОЅО± О»ОµО№П„ОїП…ПЃОіО®ПѓОµО№ ПѓП‰ПѓП„О¬ П‡П‰ПЃОЇП‚ О±П…П„О¬ П„О± cookies.","pc_title":"ОљО­ОЅП„ПЃОї О ПЃОїП„О№ОјО®ПѓОµП‰ОЅ Cookies","pc_trck_text_1":"Cookies ОµОЅП„ОїПЂО№ПѓОјОїПЌ ОєО±О№ О±ПЂОїОґОїП„О№ОєПЊП„О·П„О±П‚","pc_trck_text_2":"О‘П…П„О¬ П„О± cookies П‡ПЃО·ПѓО№ОјОїПЂОїО№ОїПЌОЅП„О±О№ ОіО№О± ОЅО± ПѓП…О»О»О­ОіОїП…ОЅ ПЂО»О·ПЃОїП†ОїПЃОЇОµП‚ ПѓП‡ОµП„О№ОєО­П‚ ОјОµ П„О·ОЅ О±ОЅО¬О»П…ПѓО· П„О·П‚ ОµПЂО№ПѓОєОµП€О№ОјПЊП„О·П„О±П‚ П„О·П‚ О№ПѓП„ОїПѓОµО»ОЇОґО±П‚ ОјО±П‚ ОєО±О№ ОјОµ П„Ої ПЂПЋП‚ ОїО№ П‡ПЃО®ПѓП„ОµП‚ П„О·ОЅ П‡ПЃО·ПѓО№ОјОїПЂОїО№ОїПЌОЅ.","pc_trck_text_3":"О“О№О± ПЂО±ПЃО¬ОґОµО№ОіОјО±, О±П…П„О¬ П„О± cookies ОјПЂОїПЃОµОЇ ОЅО± ОµОЅП„ОїПЂОЇПѓОїП…ОЅ ПЂПЊПѓОї П‡ПЃПЊОЅОї О±П†О№ОµПЃПЋОЅОµП„Оµ ПѓП„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚ О® ПЂОїО№ОµП‚ ПѓОµО»ОЇОґОµП‚ П„О·П‚ ОµПЂО№ПѓОєО­ПЂП„ОµПѓП„Оµ, ПЂПЃО¬ОіОјО± ПЂОїП… ОјО±П‚ ОІОїО·ОёО¬ОµО№ ОЅО± ОєО±П„О±О»О¬ОІОїП…ОјОµ ПЂПЋП‚ ОЅО± ОІОµО»П„О№ПЋПѓОїП…ОјОµ П„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚.","pc_trck_text_4":"ОџО№ ПЂО»О·ПЃОїП†ОїПЃОЇОµП‚ ПЂОїП… ПѓП…О»О»О­ОіОїОЅП„О±О№ ОјО­ПѓП‰ О±П…П„ПЋОЅ П„П‰ОЅ cookies ОґОµОЅ О±ОЅО±ОіОЅП‰ПЃОЇО¶ОїП…ОЅ ОјОµОјОїОЅП‰ОјО­ОЅОїП…П‚ П‡ПЃО®ПѓП„ОµП‚.","pc_trgt_text_1":"Cookies ОµОѕО±П„ОїОјО№ОєОµП…ОјО­ОЅОїП… ПЂОµПЃО№ОµП‡ОїОјО­ОЅОїП… ОєО±О№ ОґО№О±П†О·ОјОЇПѓОµП‰ОЅ","pc_trgt_text_2":"О‘П…П„О¬ П„О± cookies П‡ПЃО·ПѓО№ОјОїПЂОїО№ОїПЌОЅП„О±О№ ОіО№О± ОЅО± ОґОµОЇП‡ОЅОїП…ОЅ ОґО№О±П†О·ОјОЇПѓОµО№П‚ ПЂОїП… ОјПЂОїПЃОµОЇ ОЅО± ПѓО±П‚ ОµОЅОґО№О±П†О­ПЃОїП…ОЅ ОјОµ ОІО¬ПѓО· П„О№П‚ ПѓП…ОЅО®ОёОµО№ОµП‚ ПЂОµПЃО№О®ОіО·ПѓО®П‚ ПѓО±П‚ ПѓП„Ої О”О№О±ОґОЇОєП„П…Ої.","pc_trgt_text_3":"О‘П…П„О¬ П„О± cookies, ПЂО±ПЃО­П‡ОїОЅП„О±О№ О±ПЂПЊ П„ОїП…П‚ ПЂО±ПЃПЊП‡ОїП…П‚ ПЂОµПЃО№ОµП‡ОїОјО­ОЅОїП… О®/ОєО±О№ ОґО№О±П†О·ОјОЇПѓОµП‰ОЅ, ОјПЂОїПЃОµОЇ ОЅО± ПѓП…ОЅОґП…О¬О¶ОїП…ОЅ ПЂО»О·ПЃОїП†ОїПЃОЇОµП‚ ПЂОїП… ПѓП…О»О»О­ОіОїП…ОЅ О±ПЂПЊ П„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚ ОјОµ О¬О»О»ОµП‚ ПЂОїП… О­П‡ОїП…ОЅ О±ОЅОµОѕО¬ПЃП„О·П„О± ПѓП…О»О»О­ОѕОµО№ О±ПЂПЊ О¬О»О»О± ОґОЇОєП„П…О± О® О№ПѓП„ОїПѓОµО»ОЇОґОµП‚ ПѓП‡ОµП„О№ОєО¬ ОјОµ П„О№П‚ ОґПЃО±ПѓП„О·ПЃО№ПЊП„О·П„О­П‚ ПѓО±П‚ ПѓП„ОїОЅ П†П…О»О»ОїОјОµП„ПЃО·П„О® ПѓО±П‚.","pc_trgt_text_4":"О•О¬ОЅ ОµПЂО№О»О­ОѕОµП„Оµ ОЅО± О±П†О±О№ПЃО­ПѓОµП„Оµ О® ОЅО± О±ПЂОµОЅОµПЃОіОїПЂОїО№О®ПѓОµП„Оµ О±П…П„О¬ П„О± cookies, ОёО± ПѓП…ОЅОµП‡ОЇПѓОµП„Оµ ОЅО± ОІО»О­ПЂОµП„Оµ ОґО№О±П†О·ОјОЇПѓОµО№П‚, О±О»О»О¬ О±П…П„О­П‚ ОјПЂОїПЃОµОЇ ОЅО± ОјО·ОЅ ОµОЇОЅО±О№ ПЂО»О­ОїОЅ ПѓП‡ОµП„О№ОєО­П‚ ОјОµ П„О± ОµОЅОґО№О±П†О­ПЃОїОЅП„О¬ ПѓО±П‚.","pc_yprivacy_text_1":"О— О№ОґО№П‰П„О№ОєПЊП„О·П„О¬ ПѓО±П‚ ОµОЇОЅО±О№ ПѓО·ОјО±ОЅП„О№ОєО® ОіО№О± ОµОјО¬П‚","pc_yprivacy_text_2":"О¤О± cookies ОµОЇОЅО±О№ ПЂОїО»ПЌ ОјО№ОєПЃО¬ О±ПЃП‡ОµОЇО± ОєОµО№ОјО­ОЅОїП… ПЂОїП… О±ПЂОїОёО·ОєОµПЌОїОЅП„О±О№ ПѓП„ОїОЅ П…ПЂОїО»ОїОіО№ПѓП„О® ПѓО±П‚ ПЊП„О±ОЅ ОµПЂО№ПѓОєО­ПЂП„ОµПѓП„Оµ ОјО№О± О№ПѓП„ОїПѓОµО»ОЇОґО±. О§ПЃО·ПѓО№ОјОїПЂОїО№ОїПЌОјОµ cookies ОіО№О± ОґО№О¬П†ОїПЃОїП…П‚ О»ПЊОіОїП…П‚ ОєО±О№ ОіО№О± ОЅО± ОІОµО»П„О№ПЋПѓОїП…ОјОµ П„О·ОЅ ОґО№О±ОґО№ОєП„П…О±ОєО® ПѓО±П‚ ОµОјПЂОµО№ПЃОЇО± ПѓП„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚ (ПЂ.П‡., ОіО№О± П…ПЂОµОЅОёПЌОјО№ПѓО· П„П‰ОЅ ПѓП„ОїО№П‡ОµОЇП‰ОЅ ПЂПЃПЊПѓОІО±ПѓО®П‚ ПѓО±П‚ ПѓП„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО±).","pc_yprivacy_text_3":"ОњПЂОїПЃОµОЇП„Оµ ОЅО± О±О»О»О¬ОѕОµП„Оµ П„О№П‚ ПЂПЃОїП„О№ОјО®ПѓОµО№П‚ ПѓО±П‚ ОєО±О№ ОЅО± ОјО·ОЅ ОµПЂО№П„ПЃО­П€ОµП„Оµ ПѓОµ ОєО¬ПЂОїО№ОїП…П‚ П„ПЌПЂОїП…П‚ cookies ОЅО± О±ПЂОїОёО·ОєОµП…П„ОїПЌОЅ ПѓП„ОїОЅ П…ПЂОїО»ОїОіО№ПѓП„О® ПѓО±П‚ ПЊПѓОї ПЂОµПЃО№О·ОіОµОЇПѓП„Оµ ПѓП„О·ОЅ О№ПѓП„ОїПѓОµО»ОЇОґО± ОјО±П‚. ОњПЂОїПЃОµОЇП„Оµ ОµПЂОЇПѓО·П‚ ОЅО± ОґО№О±ОіПЃО¬П€ОµП„Оµ ОїПЂОїО№О±ОґО®ПЂОїП„Оµ cookies ОµОЇОЅО±О№ О®ОґО· О±ПЂОїОёО·ОєОµП…ОјО­ОЅО± ПѓП„ОїОЅ П…ПЂОїО»ОїОіО№ПѓП„О® ПѓО±П‚, О±О»О»О¬ ОЅО± О­П‡ОµП„Оµ П…ПЂПЊП€О№ОЅ ПЊП„О№ ОґО№О±ОіПЃО¬П†ОїОЅП„О±П‚ cookies ОјПЂОїПЃОµОЇ ОЅО± ПѓО±П‚ О±ПЂОїП„ПЃО­П€ОµО№ О±ПЂПЊ П„Ої ОЅО± П‡ПЃО·ПѓО№ОјОїПЂОїО№О®ПѓОµП„Оµ ОјО­ПЃО· П„О·П‚ О№ПѓП„ОїПѓОµО»ОЇОґО±П‚ ОјО±П‚.","pc_yprivacy_title":"О— О№ОґО№П‰П„О№ОєПЊП„О·П„О¬ ПѓО±П‚","privacy_policy":"<a href=\'%s\' target=\'_blank\'>О ОїО»О№П„О№ОєО® О±ПЂОїПЃПЃО®П„ОїП…</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Ч¤ЧўЧ™Чњ","always_active":"ЧЄЧћЧ™Ч“ Ч¤ЧўЧ™Чњ","impressum":"<a href=\'%s\' target=\'_blank\'>ЧЁЧ•Ч©Чќ</a>","inactive":"ЧњЧђ Ч¤ЧўЧ™Чњ","nb_agree":"ЧђЧ Ч™ ЧћЧЎЧ›Ч™Чќ/Ч”","nb_changep":"Ч©Ч Ч” ЧђЧЄ Ч”Ч”Ч’Ч“ЧЁЧ•ЧЄ Ч©ЧњЧ™","nb_ok":"ЧђЧ•Ч§Ч™Ч™","nb_reject":"ЧђЧ Ч™ ЧћЧЎЧЁЧ‘/ЧЄ","nb_text":"ЧђЧ Ч• ЧћЧ©ЧЄЧћЧ©Ч™Чќ Ч‘ЧўЧ•Ч’Ч™Ч•ЧЄ Ч•Ч‘ЧЧ›Ч Ч•ЧњЧ•Ч’Ч™Ч•ЧЄ ЧћЧўЧ§Ч‘ ЧђЧ—ЧЁЧ•ЧЄ Ч›Ч“Ч™ ЧњЧ©Ч¤ЧЁ ЧђЧЄ Ч—Ч•Ч•Ч™ЧЄ Ч”Ч’ЧњЧ™Ч©Ч” Ч©ЧњЧљ Ч‘ЧђЧЄЧЁ Ч”ЧђЧ™Ч ЧЧЁЧ Ч Ч©ЧњЧ Ч•, Ч›Ч“Ч™ ЧњЧ”Ч¦Ч™Ч’ ЧњЧљ ЧЄЧ•Ч›Чџ ЧћЧ•ЧЄЧђЧќ ЧђЧ™Ч©Ч™ЧЄ Ч•ЧћЧ•Ч“ЧўЧ•ЧЄ ЧћЧћЧ•Ч§Ч“Ч•ЧЄ, ЧњЧ ЧЄЧ— ЧђЧЄ Ч”ЧЄЧ Ч•ЧўЧ” Ч‘ЧђЧЄЧЁ Ч©ЧњЧ Ч• Ч•ЧњЧ”Ч‘Ч™Чџ ЧћЧ”Ч™Ч›Чџ ЧћЧ’Ч™ЧўЧ™Чќ Ч”ЧћЧ‘Ч§ЧЁЧ™Чќ Ч©ЧњЧ Ч•.","nb_title":"ЧђЧ Ч• ЧћЧ©ЧЄЧћЧ©Ч™Чќ Ч‘ЧўЧ•Ч’Ч™Ч•ЧЄ","pc_fnct_text_1":"ЧўЧ•Ч’Ч™Ч•ЧЄ Ч¤Ч•Ч Ч§Ч¦Ч™Ч•Ч ЧњЧ™Ч•ЧЄ","pc_fnct_text_2":"ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ” ЧћЧ©ЧћЧ©Ч•ЧЄ Ч›Ч“Ч™ ЧњЧЎЧ¤Ч§ ЧњЧљ Ч—Ч•Ч•Ч™Ч” ЧћЧ•ЧЄЧђЧћЧЄ ЧђЧ™Ч©Ч™ЧЄ Ч™Ч•ЧЄЧЁ Ч‘ЧђЧЄЧЁ Ч”ЧђЧ™Ч ЧЧЁЧ Ч Ч©ЧњЧ Ч• Ч•Ч›Ч“Ч™ ЧњЧ–Ч›Ч•ЧЁ Ч‘Ч—Ч™ЧЁЧ•ЧЄ Ч©ЧђЧЄЧ” ЧўЧ•Ч©Ч” Ч›Ч©ЧђЧЄЧ” ЧћЧ©ЧЄЧћЧ© Ч‘ЧђЧЄЧЁ Ч©ЧњЧ Ч•.","pc_fnct_text_3":"ЧњЧ“Ч•Ч’ЧћЧ”, ЧђЧ Ч• ЧўЧ©Ч•Ч™Ч™Чќ ЧњЧ”Ч©ЧЄЧћЧ© Ч‘ЧўЧ•Ч’Ч™Ч•ЧЄ Ч¤Ч•Ч Ч§Ч¦Ч™Ч•Ч ЧњЧ™Ч•ЧЄ Ч›Ч“Ч™ ЧњЧ–Ч›Ч•ЧЁ ЧђЧЄ Ч”ЧўЧ“Ч¤Ч•ЧЄ Ч”Ч©Ч¤Ч” Ч©ЧњЧљ ЧђЧ• ЧњЧ–Ч›Ч•ЧЁ ЧђЧЄ Ч¤ЧЁЧЧ™ Ч”Ч”ЧЄЧ—Ч‘ЧЁЧ•ЧЄ Ч©ЧњЧљ.","pc_minfo_text_1":"ЧћЧ™Ч“Чў Ч Ч•ЧЎЧЈ","pc_minfo_text_2":"ЧњЧ›Чњ Ч©ЧђЧњЧ” Ч‘Ч Ч•Ч’Чў ЧњЧћЧ“Ч™Ч Ч™Ч•ЧЄ Ч©ЧњЧ Ч• Ч‘Ч Ч•Ч©Чђ Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ Ч•Ч”Ч‘Ч—Ч™ЧЁЧ•ЧЄ Ч©ЧњЧљ, ЧђЧ Чђ Ч¦Ч•ЧЁ ЧђЧ™ЧЄЧ Ч• Ч§Ч©ЧЁ.","pc_minfo_text_3":"ЧњЧћЧ™Ч“Чў Ч Ч•ЧЎЧЈ, Ч‘Ч§ЧЁ Ч‘<a href=\'%s\' target=\'_blank\'>ЧћЧ“Ч™Ч Ч™Ч•ЧЄ Ч”Ч¤ЧЁЧЧ™Ч•ЧЄ</a> Ч©ЧњЧ Ч•.","pc_save":"Ч©ЧћЧ•ЧЁ ЧђЧЄ Ч”Ч”ЧўЧ“Ч¤Ч•ЧЄ Ч©ЧњЧ™","pc_sncssr_text_1":"ЧўЧ•Ч’Ч™Ч•ЧЄ Ч Ч—Ч•Ч¦Ч•ЧЄ Ч‘ЧњЧ‘Ч“","pc_sncssr_text_2":"ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ• Ч—Ч™Ч•Ч Ч™Ч•ЧЄ Ч›Ч“Ч™ ЧњЧЎЧ¤Ч§ ЧњЧљ Ч©Ч™ЧЁЧ•ЧЄЧ™Чќ Ч”Ч–ЧћЧ™Ч Ч™Чќ Ч“ЧЁЧљ Ч”ЧђЧЄЧЁ Ч©ЧњЧ Ч• Ч•Ч›Ч“Ч™ ЧњЧђЧ¤Ч©ЧЁ ЧњЧљ ЧњЧ”Ч©ЧЄЧћЧ© Ч‘ЧЄЧ›Ч•Ч Ч•ЧЄ ЧћЧЎЧ•Ч™ЧћЧ•ЧЄ Ч©Чњ Ч”ЧђЧЄЧЁ Ч©ЧњЧ Ч•.","pc_sncssr_text_3":"ЧњЧњЧђ ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ”, ЧђЧ™Ч Ч Ч• Ч™Ч›Ч•ЧњЧ™Чќ ЧњЧЎЧ¤Ч§ ЧњЧљ Ч©Ч™ЧЁЧ•ЧЄЧ™Чќ ЧћЧЎЧ•Ч™ЧћЧ™Чќ Ч‘ЧђЧЄЧЁ Ч©ЧњЧ Ч•.","pc_title":"ЧћЧЁЧ›Ч– Ч”ЧўЧ“Ч¤Ч•ЧЄ ЧўЧ•Ч’Ч™Ч•ЧЄ","pc_trck_text_1":"ЧўЧ•Ч’Ч™Ч•ЧЄ ЧћЧўЧ§Ч‘","pc_trck_text_2":"ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ• ЧћЧ©ЧћЧ©Ч•ЧЄ ЧњЧђЧ™ЧЎЧ•ЧЈ ЧћЧ™Ч“Чў Ч›Ч“Ч™ ЧњЧ ЧЄЧ— ЧђЧЄ Ч”ЧЄЧ Ч•ЧўЧ” ЧњЧђЧЄЧЁ Ч©ЧњЧ Ч• Ч•Ч›Ч™Ч¦Ч“ Ч”ЧћЧ‘Ч§ЧЁЧ™Чќ ЧћЧ©ЧЄЧћЧ©Ч™Чќ Ч‘ЧђЧЄЧЁ Ч©ЧњЧ Ч•.","pc_trck_text_3":"ЧњЧ“Ч•Ч’ЧћЧ”, Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ” ЧўЧ©Ч•Ч™Ч™Чќ ЧњЧўЧ§Ч•Ч‘ ЧђЧ—ЧЁ Ч“Ч‘ЧЁЧ™Чќ Ч›Ч’Ч•Чџ ЧћЧ©Чљ Ч”Ч–ЧћЧџ Ч©ЧђЧЄЧ” ЧћЧ‘ЧњЧ” Ч‘ЧђЧЄЧЁ ЧђЧ• Ч”Ч“Ч¤Ч™Чќ Ч©Ч‘Ч”Чќ ЧђЧЄЧ” ЧћЧ‘Ч§ЧЁ, ЧћЧ” Ч©ЧўЧ•Ч–ЧЁ ЧњЧ Ч• ЧњЧ”Ч‘Ч™Чџ Ч›Ч™Ч¦Ч“ ЧђЧ Ч• Ч™Ч›Ч•ЧњЧ™Чќ ЧњЧ©Ч¤ЧЁ ЧўЧ‘Ч•ЧЁЧљ ЧђЧЄ ЧђЧЄЧЁ Ч”ЧђЧ™Ч ЧЧЁЧ Ч Ч©ЧњЧ Ч•.","pc_trck_text_4":"Ч”ЧћЧ™Ч“Чў Ч©Ч ЧђЧЎЧЈ Ч‘ЧђЧћЧ¦ЧўЧ•ЧЄ ЧўЧ•Ч’Ч™Ч•ЧЄ ЧћЧўЧ§Ч‘ Ч•Ч‘Ч™Ч¦Ч•ЧўЧ™Чќ ЧђЧњЧ” ЧђЧ™Ч Ч• ЧћЧ–Ч”Ч” ЧђЧЈ ЧћЧ‘Ч§ЧЁ Ч‘Ч•Ч“Ч“.","pc_trgt_text_1":"ЧўЧ•Ч’Ч™Ч•ЧЄ ЧћЧ™Ч§Ч•Ч“ Ч•Ч¤ЧЁЧЎЧ•Чќ","pc_trgt_text_2":"ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ• ЧћЧ©ЧћЧ©Ч•ЧЄ ЧњЧ”Ч¦Ч’ЧЄ Ч¤ЧЁЧЎЧ•ЧћЧ•ЧЄ Ч©ЧЎЧ‘Ч™ЧЁ ЧњЧ”Ч Ч™Ч— Ч©Ч™ЧўЧ Ч™Ч™Ч Ч• ЧђЧ•ЧЄЧљ Ч‘Ч”ЧЄЧ‘ЧЎЧЎ ЧўЧњ Ч”ЧЁЧ’ЧњЧ™ Ч”Ч’ЧњЧ™Ч©Ч” Ч©ЧњЧљ.","pc_trgt_text_3":"Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ ЧђЧњЧ”, Ч›Ч¤Ч™ Ч©ЧћЧ•Ч¦Ч’Ч™Чќ ЧўЧњ Ч™Ч“Ч™ ЧЎЧ¤Ч§Ч™ Ч”ЧЄЧ•Ч›Чџ Ч•/ЧђЧ• Ч”Ч¤ЧЁЧЎЧ•Чќ Ч©ЧњЧ Ч•, ЧўЧ©Ч•Ч™Ч™Чќ ЧњЧ©ЧњЧ‘ ЧћЧ™Ч“Чў Ч©Ч”Чќ ЧђЧЎЧ¤Ч• ЧћЧ”ЧђЧЄЧЁ Ч©ЧњЧ Ч• ЧўЧќ ЧћЧ™Ч“Чў ЧђЧ—ЧЁ Ч©Ч”Чќ ЧђЧЎЧ¤Ч• Ч‘ЧђЧ•Ч¤Чџ ЧўЧ¦ЧћЧђЧ™ Ч”Ч§Ч©Ч•ЧЁ ЧњЧ¤ЧўЧ™ЧњЧ•Ч™Ч•ЧЄ Ч©Чњ Ч“Ч¤Ч“Ч¤Чџ Ч”ЧђЧ™Ч ЧЧЁЧ Ч Ч©ЧњЧљ Ч‘ЧЁЧ—Ч‘Ч™ ЧЁЧ©ЧЄ Ч”ЧђЧЄЧЁЧ™Чќ Ч©ЧњЧ”Чќ.","pc_trgt_text_4":"ЧђЧќ ЧЄЧ‘Ч—ЧЁ ЧњЧ”ЧЎЧ™ЧЁ ЧђЧ• ЧњЧ”Ч©Ч‘Ч™ЧЄ ЧђЧЄ Ч§Ч•Ч‘Ч¦Ч™ Ч”ЧћЧ™Ч§Ч•Ч“ ЧђЧ• Ч§Ч•Ч‘Ч¦Ч™ Ч”Ч¤ЧЁЧЎЧ•Чќ Ч”ЧњЧњЧ•, ЧўЧ“Ч™Ч™Чџ ЧЄЧЁЧђЧ” Ч¤ЧЁЧЎЧ•ЧћЧ•ЧЄ ЧђЧљ Ч™Ч™ЧЄЧ›Чџ Ч©Ч”Чџ ЧњЧђ Ч™Ч”Ч™Ч• ЧЁЧњЧ•Ч•Ч ЧЧ™Ч•ЧЄ ЧўЧ‘Ч•ЧЁЧљ.","pc_yprivacy_text_1":"Ч”Ч¤ЧЁЧЧ™Ч•ЧЄ Ч©ЧњЧљ Ч—Ч©Ч•Ч‘Ч” ЧњЧ Ч•","pc_yprivacy_text_2":"Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ Ч”Чќ Ч§Ч‘Ч¦Ч™ ЧЧ§ЧЎЧ Ч§ЧЧ Ч™Чќ ЧћЧђЧ•Ч“ Ч”ЧћЧђЧ•Ч—ЧЎЧ Ч™Чќ Ч‘ЧћЧ—Ч©Ч‘ Ч©ЧњЧљ Ч›ЧђЧ©ЧЁ ЧђЧЄЧ” ЧћЧ‘Ч§ЧЁ Ч‘ЧђЧЄЧЁ. ЧђЧ Ч• ЧћЧ©ЧЄЧћЧ©Ч™Чќ Ч‘Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ ЧњЧћЧ’Ч•Ч•Чџ ЧћЧЧЁЧ•ЧЄ Ч•Ч›Ч“Ч™ ЧњЧ©Ч¤ЧЁ ЧђЧЄ Ч”Ч—Ч•Ч•Ч™Ч” Ч”ЧћЧ§Ч•Ч•Ч ЧЄ Ч©ЧњЧљ Ч‘ЧђЧЄЧЁ Ч”ЧђЧ™Ч ЧЧЁЧ Ч Ч©ЧњЧ Ч• (ЧњЧ“Ч•Ч’ЧћЧ”, Ч›Ч“Ч™ ЧњЧ–Ч›Ч•ЧЁ ЧђЧЄ Ч¤ЧЁЧЧ™ Ч”Ч›Ч Ч™ЧЎЧ” ЧњЧ—Ч©Ч‘Ч•Чџ Ч©ЧњЧљ).","pc_yprivacy_text_3":"ЧђЧЄЧ” Ч™Ч›Ч•Чњ ЧњЧ©Ч Ч•ЧЄ ЧђЧЄ Ч”Ч”ЧўЧ“Ч¤Ч•ЧЄ Ч©ЧњЧљ Ч•ЧњЧ“Ч—Ч•ЧЄ ЧЎЧ•Ч’Ч™Чќ ЧћЧЎЧ•Ч™ЧћЧ™Чќ Ч©Чњ ЧўЧ•Ч’Ч™Ч•ЧЄ Ч©Ч™Ч©ЧћЧЁЧ• Ч‘ЧћЧ—Ч©Ч‘ Ч©ЧњЧљ Ч‘Ч–ЧћЧџ Ч”Ч’ЧњЧ™Ч©Ч” Ч‘ЧђЧЄЧЁ Ч©ЧњЧ Ч•. ЧђЧЄЧ” Ч™Ч›Ч•Чњ Ч’Чќ ЧњЧ”ЧЎЧ™ЧЁ Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ Ч©Ч›Ч‘ЧЁ ЧћЧђЧ•Ч—ЧЎЧ Ч™Чќ Ч‘ЧћЧ—Ч©Ч‘ Ч©ЧњЧљ, ЧђЧљ Ч–Ч›Ч•ЧЁ Ч©ЧћЧ—Ч™Ч§ЧЄ Ч§Ч•Ч‘Ч¦Ч™ ЧўЧ•Ч’Ч™Ч•ЧЄ ЧўЧњЧ•ЧњЧ” ЧњЧћЧ Ч•Чў ЧћЧћЧљ ЧњЧ”Ч©ЧЄЧћЧ© Ч‘Ч—ЧњЧ§Ч™Чќ ЧћЧ”ЧђЧЄЧЁ Ч©ЧњЧ Ч•.","pc_yprivacy_title":"Ч”Ч¤ЧЁЧЧ™Ч•ЧЄ Ч©ЧњЧљ","privacy_policy":"<a href=\'%s\' target=\'_blank\'>ЧћЧ“Ч™Ч Ч™Ч•ЧЄ Ч¤ЧЁЧЧ™Ч•ЧЄ</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"РђРєС‚РёРІРЅРѕ","always_active":"РЎРµРєРѕРіР°С€ Р°РєС‚РёРІРЅРѕ","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"РќРµР°РєС‚РёРІРЅРѕ","nb_agree":"РЎРµ СЃРѕРіР»Р°СЃСѓРІР°Рј","nb_changep":"РџСЂРѕРјРµРЅРё РіРё РјРѕРёС‚Рµ РїСЂРµС„РµСЂРµРЅС†РёРё","nb_ok":"РЎРµ СЃРѕРіР»Р°СЃСѓРІР°Рј","nb_reject":"РћРґР±РёРІР°Рј","nb_text":"РќРёРµ РєРѕСЂРёСЃС‚РёРјРµ РєРѕР»Р°С‡РёСљР° Рё РґСЂСѓРіРё С‚РµС…РЅРѕР»РѕРіРёРё Р·Р° СЃР»РµРґРµСљРµ Р·Р° РґР° РіРѕ РїРѕРґРѕР±СЂРёРјРµ РІР°С€РµС‚Рѕ РёСЃРєСѓСЃС‚РІРѕ СЃРѕ РїСЂРµР»РёСЃС‚СѓРІР°СљРµС‚Рѕ РЅР° РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°, Р·Р° РґР° РІРё РїСЂРёРєР°Р¶РµРјРµ РїРµСЂСЃРѕРЅР°Р»РёР·РёСЂР°РЅР° СЃРѕРґСЂР¶РёРЅР° Рё С‚Р°СЂРіРµС‚РёСЂР°РЅРё СЂРµРєР»Р°РјРё, РґР° РіРѕ Р°РЅР°Р»РёР·РёСЂР°РјРµ СЃРѕРѕР±СЂР°СњР°СРѕС‚ РЅР° РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР° Рё РґР° СЂР°Р·Р±РµСЂРµРјРµ РѕРґ РєР°РґРµ РґРѕР°С“Р°Р°С‚ РЅР°С€РёС‚Рµ РїРѕСЃРµС‚РёС‚РµР»Рё.","nb_title":"РќРёРµ РєРѕСЂРёСЃС‚РёРјРµ РєРѕР»Р°С‡РёСљР°","pc_fnct_text_1":"РљРѕР»Р°С‡РёСљР° Р·Р° С„СѓРЅРєС†РёРѕРЅР°Р»РЅРѕСЃС‚","pc_fnct_text_2":"РћРІРёРµ РєРѕР»Р°С‡РёСљР° СЃРµ РєРѕСЂРёСЃС‚Р°С‚ Р·Р° РґР° РІРё РѕРІРѕР·РјРѕР¶Р°С‚ РїРѕРїРµСЂСЃРѕРЅР°Р»РёР·РёСЂР°РЅРѕ РёСЃРєСѓСЃС‚РІРѕ РЅР° РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР° Рё РґР° РіРё Р·Р°РїРѕРјРЅР°С‚ РёР·Р±РѕСЂРёС‚Рµ С€С‚Рѕ РіРё РїСЂР°РІРёС‚Рµ РєРѕРіР° СР° РєРѕСЂРёСЃС‚РёС‚Рµ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°.","pc_fnct_text_3":"РќР° РїСЂРёРјРµСЂ, РјРѕР¶Рµ РґР° РєРѕСЂРёСЃС‚РёРјРµ С„СѓРЅРєС†РёРѕРЅР°Р»РЅРё РєРѕР»Р°С‡РёСљР° Р·Р° РґР° РіРё Р·Р°РїРѕРјРЅРёРјРµ РІР°С€РёС‚Рµ СР°Р·РёС‡РЅРё РїСЂРµС„РµСЂРµРЅС†РёРё РёР»Рё РґР° РіРё Р·Р°РїРѕРјРЅРёРјРµ РІР°С€РёС‚Рµ РґРµС‚Р°Р»Рё Р·Р° РЅР°СР°РІСѓРІР°СљРµ.","pc_minfo_text_1":"РџРѕРІРµСњРµ РёРЅС„РѕСЂРјР°С†РёРё","pc_minfo_text_2":"Р—Р° Р±РёР»Рѕ РєР°РєРІРё РїСЂР°С€Р°СљР° РІРѕ РІСЂСЃРєР° СЃРѕ РЅР°С€Р°С‚Р° РїРѕР»РёС‚РёРєР° Р·Р° РєРѕР»Р°С‡РёСљР° Рё РІР°С€РёРѕС‚ РёР·Р±РѕСЂ, РІРµ РјРѕР»РёРјРµ РєРѕРЅС‚Р°РєС‚РёСЂР°СС‚Рµ РЅРµ.","pc_minfo_text_3":"Р—Р° РґР° РґРѕР·РЅР°РµС‚Рµ РїРѕРІРµСњРµ, РІРµ РјРѕР»РёРјРµ РїРѕСЃРµС‚РµС‚Рµ СР° РЅР°С€Р°С‚Р° <a href=\'%s\' target=\'_blank\'>РџРѕР»РёС‚РёРєР° Р·Р° РџСЂРёРІР°С‚РЅРѕСЃС‚</a>.","pc_save":"Р—Р°С‡СѓРІР°С РіРё РјРѕРёС‚Рµ РїСЂРµС„РµСЂРµРЅС†РёРё","pc_sncssr_text_1":"РЎС‚СЂРѕРіРѕ РЅРµРѕРїС…РѕРґРЅРё РєРѕР»Р°С‡РёСљР°","pc_sncssr_text_2":"РћРІРёРµ РєРѕР»Р°С‡РёСљР° СЃРµ РѕРґ СЃСѓС€С‚РёРЅСЃРєРѕ Р·РЅР°С‡РµСљРµ Р·Р° РґР° РІРё РѕРІРѕР·РјРѕР¶Р°С‚ СѓСЃР»СѓРіРё РґРѕСЃС‚Р°РїРЅРё РїСЂРµРєСѓ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°, Рё РґР° РІРё РѕРІРѕР·РјРѕР¶Р°С‚ РґР° РєРѕСЂРёСЃС‚РёС‚Рµ РѕРґСЂРµРґРµРЅРё С„СѓРЅРєС†РёРё РЅР° РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°.","pc_sncssr_text_3":"Р‘РµР· РѕРІРёРµ РєРѕР»Р°С‡РёСљР°, РЅРёРµ РЅРµ РјРѕР¶РµРјРµ РґР° РІРё РѕР±РµР·Р±РµРґРёРјРµ РѕРґСЂРµРґРµРЅРё СѓСЃР»СѓРіРё РЅР° РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°.","pc_title":"Р¦РµРЅС‚Р°СЂ Р·Р° РїСЂРµС„РµСЂРµРЅС†Рё Р·Р° РєРѕР»Р°С‡РёСљР°","pc_trck_text_1":"РљРѕР»Р°С‡РёСљР° Р·Р° СЃР»РµРґРµСљРµ","pc_trck_text_2":"РћРІРёРµ РєРѕР»Р°С‡РёСљР° СЃРµ РєРѕСЂРёСЃС‚Р°С‚ Р·Р° СЃРѕР±РёСЂР°СљРµ РёРЅС„РѕСЂРјР°С†РёРё Р·Р° Р°РЅР°Р»РёР·Р° РЅР° СЃРѕРѕР±СЂР°СњР°СРѕС‚ РєРѕРЅ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°, Рё Р·Р° С‚РѕР° РєР°РєРѕ РїРѕСЃРµС‚РёС‚РµР»РёС‚Рµ СР° РєРѕСЂРёСЃС‚Р°С‚ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°.","pc_trck_text_3":"РћРІРёРµ РєРѕР»Р°С‡РёСљР° РјРѕР¶Рµ РґР° СЃР»РµРґР°С‚ СЂР°Р±РѕС‚Рё РєР°РєРѕ РЅР° РїСЂРёРјРµСЂ, РєРѕР»РєСѓ РІСЂРµРјРµ РїРѕРјРёРЅСѓРІР°С‚Рµ РЅР° РІРµР± СЃС‚СЂР°РЅР°С‚Р°, РёР»Рё СЃС‚СЂР°РЅРёС†РёС‚Рµ С€С‚Рѕ РіРё РїРѕСЃРµС‚СѓРІР°С‚Рµ С€С‚Рѕ РЅРё РїРѕРјР°РіР° РґР° СЂР°Р·Р±РµСЂРµРјРµ РєР°РєРѕ РјРѕР¶РµРјРµ РґР° СР° РїРѕРґРѕР±СЂРёРјРµ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР° Р·Р° РІР°СЃ.","pc_trck_text_4":"РРЅС„РѕСЂРјР°С†РёРёС‚Рµ СЃРѕР±СЂР°РЅРё РїСЂРµРєСѓ РѕРІРёРµ РєРѕР»Р°С‡РёСљР° Р·Р° СЃР»РµРґРµСљРµ Рё РїРµСЂС„РѕСЂРјР°РЅСЃРё РЅРµ РёРґРµРЅС‚РёС„РёРєСѓРІР°Р°С‚ РїРѕРµРґРёРЅРµС‡РЅРё РїРѕСЃРµС‚РёС‚РµР»Рё.","pc_trgt_text_1":"РљРѕР»Р°С‡РёСљР° Р·Р° С‚Р°СЂРіРµС‚РёСЂР°СљРµ Рё СЂРµРєР»Р°РјРёСЂР°СљРµ","pc_trgt_text_2":"РћРІРёРµ РєРѕР»Р°С‡РёСљР° СЃРµ РєРѕСЂРёСЃС‚Р°С‚ Р·Р° РїСЂРёРєР°Р¶СѓРІР°СљРµ СЂРµРєР»Р°РјРё С€С‚Рѕ РЅР°СРІРµСЂРѕСР°С‚РЅРѕ СњРµ РІРµ РёРЅС‚РµСЂРµСЃРёСЂР°Р°С‚ РІСЂР· РѕСЃРЅРѕРІР° РЅР° РІР°С€РёС‚Рµ РЅР°РІРёРєРё РЅР° РїСЂРµР»РёСЃС‚СѓРІР°СљРµ.","pc_trgt_text_3":"РћРІРёРµ РєРѕР»Р°С‡РёСљР°, СЃРµСЂРІРёСЂР°РЅРё РѕРґ РЅР°С€Р°С‚Р° СЃРѕРґСЂР¶РёРЅР° Рё/РёР»Рё РїСЂРѕРІР°СРґРµСЂРё Р·Р° СЂРµРєР»Р°РјРёСЂР°СљРµ, РјРѕР¶Рµ РґР° РіРё РєРѕРјР±РёРЅРёСЂР°Р°С‚ РёРЅС„РѕСЂРјР°С†РёРёС‚Рµ С€С‚Рѕ РіРё СЃРѕР±СЂР°Р»Рµ РѕРґ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР° СЃРѕ РґСЂСѓРіРё РёРЅС„РѕСЂРјР°С†РёРё С€С‚Рѕ РЅРµР·Р°РІРёСЃРЅРѕ РіРё СЃРѕР±СЂР°Р»Рµ РІРѕ РІСЂСЃРєР° СЃРѕ Р°РєС‚РёРІРЅРѕСЃС‚РёС‚Рµ РЅР° РІР°С€РёРѕС‚ РІРµР±-РїСЂРµР»РёСЃС‚СѓРІР°С‡ РЅРёР· РЅРёРІРЅР°С‚Р° РјСЂРµР¶Р° РЅР° РІРµР± СЃС‚СЂР°РЅРё.","pc_trgt_text_4":"РђРєРѕ РёР·Р±РµСЂРµС‚Рµ РґР° РіРё РѕС‚СЃС‚СЂР°РЅРёС‚Рµ РёР»Рё РѕРЅРµРІРѕР·РјРѕР¶РёС‚Рµ РѕРІРёРµ РєРѕР»Р°С‡РёСљР° Р·Р° С‚Р°СЂРіРµС‚РёСЂР°СљРµ РёР»Рё СЂРµРєР»Р°РјРёСЂР°СљРµ, СЃГЁ СѓС€С‚Рµ СњРµ РіР»РµРґР°С‚Рµ СЂРµРєР»Р°РјРё, РЅРѕ С‚РёРµ РјРѕР¶РµР±Рё РЅРµРјР° РґР° Р±РёРґР°С‚ СЂРµР»РµРІР°РЅС‚РЅРё Р·Р° РІР°СЃ.","pc_yprivacy_text_1":"Р’Р°С€Р°С‚Р° РїСЂРёРІР°С‚РЅРѕСЃС‚ Рµ РІР°Р¶РЅР° Р·Р° РЅР°СЃ","pc_yprivacy_text_2":"РљРѕР»Р°С‡РёСљР°С‚Р° СЃРµ РјРЅРѕРіСѓ РјР°Р»Рё С‚РµРєСЃС‚СѓР°Р»РЅРё РґР°С‚РѕС‚РµРєРё С€С‚Рѕ СЃРµ СЃРєР»Р°РґРёСЂР°Р°С‚ РЅР° РІР°С€РёРѕС‚ РєРѕРјРїССѓС‚РµСЂ РєРѕРіР° РїРѕСЃРµС‚СѓРІР°С‚Рµ РІРµР± СЃС‚СЂР°РЅР°. РќРёРµ РєРѕСЂРёСЃС‚РёРјРµ РєРѕР»Р°С‡РёСљР° Р·Р° СЂР°Р·Р»РёС‡РЅРё С†РµР»Рё Рё Р·Р° РґР° РіРѕ РїРѕРґРѕР±СЂРёРјРµ РІР°С€РµС‚Рѕ РѕРЅР»Р°СРЅ РёСЃРєСѓСЃС‚РІРѕ РЅР° РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР° (РЅР° РїСЂРёРјРµСЂ, Р·Р° РґР° РіРё Р·Р°РїРѕРјРЅРёРјРµ РґРµС‚Р°Р»РёС‚Рµ Р·Р° РЅР°СР°РІСѓРІР°СљРµ РЅР° РІР°С€Р°С‚Р° СЃРјРµС‚РєР°).","pc_yprivacy_text_3":"РњРѕР¶РµС‚Рµ РґР° РіРё РїСЂРѕРјРµРЅРёС‚Рµ РІР°С€РёС‚Рµ РїР°СЂР°РјРµС‚СЂРё Рё РґР° РѕРґР±РёРµС‚Рµ РѕРґСЂРµРґРµРЅРё РІРёРґРѕРІРё РєРѕР»Р°С‡РёСљР° РґР° СЃРµ СЃРєР»Р°РґРёСЂР°Р°С‚ РЅР° РІР°С€РёРѕС‚ РєРѕРјРїССѓС‚РµСЂ РґРѕРґРµРєР° СР° РїСЂРµР»РёСЃС‚СѓРІР°С‚Рµ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°. РњРѕР¶РµС‚Рµ РёСЃС‚Рѕ С‚Р°РєР° РґР° РіРё РѕС‚СЃС‚СЂР°РЅРёС‚Рµ СЃРёС‚Рµ РєРѕР»Р°С‡РёСљР° С€С‚Рѕ СЃРµ РІРµСњРµ Р·Р°С‡СѓРІР°РЅРё РЅР° РІР°С€РёРѕС‚ РєРѕРјРїССѓС‚РµСЂ, РЅРѕ РёРјР°СС‚Рµ РІРѕ РїСЂРµРґРІРёРґ РґРµРєР° Р±СЂРёС€РµСљРµС‚Рѕ РєРѕР»Р°С‡РёСљР° РјРѕР¶Рµ РґР° РІРµ СЃРїСЂРµС‡Рё РґР° РєРѕСЂРёСЃС‚РёС‚Рµ РґРµР»РѕРІРё РѕРґ РЅР°С€Р°С‚Р° РІРµР± СЃС‚СЂР°РЅР°.","pc_yprivacy_title":"Р’Р°С€Р°С‚Р° РїСЂРёРІР°С‚РЅРѕСЃС‚","privacy_policy":"<a href=\'%s\' target=\'_blank\'>РџРѕР»РёС‚РёРєР° Р·Р° РџСЂРёРІР°С‚РЅРѕСЃС‚</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Gweithredol","always_active":"Yn weithredol bob tro","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Anweithredol","nb_agree":"Rwy\'n cytuno","nb_changep":"Newid fy newisiadau","nb_ok":"Iawn","nb_reject":"Rwy\'n gwrthod","nb_text":"Rydym yn defnyddio cwcis a thechnolegau tracio eraill i wella eich profiad o bori ar ein gwefan, i ddangos cynnwys wedi ei bersonoli a hysbysebion wedi\'u targedu, i ddadansoddi traffig ar ein gwefan ac i ddeall o ble daw ein hymwelwyr.","nb_title":"Rydym yn defnyddio cwcis","pc_fnct_text_1":"Cwcis swyddogaeth","pc_fnct_text_2":"Mae\'r cwcis yma yn cael eu defnyddio i ddarparu profiad mwy personol ichi ar ein gwefan, ac i gofio dewisiadau a wnewch wrth ddefnyddio ein gwefan.","pc_fnct_text_3":"Er enghraifft, gallem ddefnyddio cwcis swyddogaeth i gofio\'ch dewis iaith neu gofio\'ch manylion mewngofnodi.","pc_minfo_text_1":"Rhagor o wybodaeth","pc_minfo_text_2":"Os oes gennych chi unrhyw ymholiadau yn ymwneud Гў\'n polisi cwcis a\'ch dewisiadau, a wnewch chi gysylltu Гў ni.","pc_minfo_text_3":"I ganfod mwy, ewch at ein <a href=\'%s\' target=\'_blank\'>PolasaГ­ PrГ­obhГЎideachta</a>.","pc_save":"Cadw fy newisiadau","pc_sncssr_text_1":"Cwcis hollol hanfodol","pc_sncssr_text_2":"Mae\'r cwcis yma yn hanfodol er mwyn ichi dderbyn gwasanaethau drwy ein gwefan a\'ch galluogi i ddefnyddio nodweddion penodol ar ein gwefan.","pc_sncssr_text_3":"Heb y cwcis yma, ni fedrwn ddarparu rhai gwasanaethau penodol ichi ar ein gwefan.","pc_title":"Canolfan Dewisiadau Cwcis","pc_trck_text_1":"Cwcis tracio a pherfformiad","pc_trck_text_2":"Mae\'r cwcis yma yn cael eu defnyddio i gasglu gwybodaeth a dadansoddi traffig i\'n gwefan a sut mae ymwelwyr yn defnyddio\'n gwefan.","pc_trck_text_3":"Er enghraifft, gall y cwcis yma dracio faint o amser rydych yn ei dreulio ar y wefan neu\'r tudalennau rydych yn ymweld Гў hwy a\'n cynorthwyo i ddeall sut y gallwn wella ein gwefan ar eich cyfer.","pc_trck_text_4":"Nid yw\'r wybodaeth a gesglir drwy\'r cwcis tracio a pherfformiad yn adnabod unrhyw ymwelydd unigol.","pc_trgt_text_1":"Cwcis targedu a hysbysebu","pc_trgt_text_2":"Mae\'r cwcis yma yn cael eu defnyddio i ddangos hysbysebion sydd yn debygol o fod o ddiddordeb i chi yn seiliedig ar eich arferion pori.","pc_trgt_text_3":"Gall y cwcis yma, fel y\'u gweinyddir gan ein darparwyr cynnwys a/neu hysbysebion, gyfuno gwybodaeth a gasglwyd ganddynt o\'n gwefan gyda gwybodaeth arall maent wedi ei chasglu\'n annibynnol yn seiliedig ar eich gweithgareddau pori ar y rhyngrwyd ar draws eu rhwydweithiau o wefannau.","pc_trgt_text_4":"Os byddwch yn dewis tynnu neu atal y cwcis targedu neu hysbysebu yma, byddwch yn parhau i weld hysbysebion ond mae\'n bosib na fyddant yn berthnasol i chi.","pc_yprivacy_text_1":"Mae eich preifatrwydd yn bwysig i ni","pc_yprivacy_text_2":"Ffeiliau testun bach eu maint yw cwcis sydd yn cael eu storio ar eich cyfrifiadur wrth ichi ymweld Гў gwefan. Rydym yn defnyddio cwcis i sawl diben ac i wella eich profiad ar-lein ar ein gwefan (er enghraifft, cofio eich manylion mewngofnodi i\'ch cyfrif).","pc_yprivacy_text_3":"Gallwch newid eich dewisiadau ac atal rhai mathau o gwcis rhag cael eu storio ar eich cyfrifiadur. Gallwch hefyd dynnu unrhyw gwcis sydd eisoes wedi eu storio ar eich cyfrifiadur, ond cofiwch y gall.","pc_yprivacy_title":"Eich preifatrwydd","privacy_policy":"<a href=\'%s\' target=\'_blank\'>PolasaГ­ PrГ­obhГЎideachta</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"г‚ўг‚Їгѓ†г‚Јгѓ–","always_active":"еёёгЃ«г‚ўг‚Їгѓ†г‚Јгѓ–","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"еЃњж­ўдё­","nb_agree":"еђЊж„Џ","nb_changep":"иЁ­е®ље¤‰ж›ґ","nb_ok":"ж‰їи«ѕ","nb_reject":"ж‹’еђ¦","nb_text":"иЁЄе•ЏиЂ…гЃ®еЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ®й–Іи¦§дЅ“йЁ“г‚’еђ‘дёЉгЃ•гЃ›г‚‹гЃџг‚ЃгЂЃгѓ‘гѓјг‚ЅгѓЉгѓ©г‚¤г‚єгЃ•г‚ЊгЃџг‚ігѓігѓ†гѓігѓ„г‚„г‚їгѓјг‚Ігѓѓгѓ€еєѓе‘Љг‚’иЎЁз¤єгЃ™г‚‹гЃџг‚ЃгЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ®гѓ€гѓ©гѓ•г‚Јгѓѓг‚Їг‚’е€†жћђгЃ™г‚‹гЃџг‚ЃгЂЃгЃЉг‚€гЃіеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃёгЃ®иЁЄе•ЏиЂ…гЃЊгЃ©гЃ“гЃ‹г‚‰жќҐгЃ¦гЃ„г‚‹гЃ‹г‚’зђ†и§ЈгЃ™г‚‹гЃџг‚ЃгЃ«гЂЃCookieгЃЉг‚€гЃігЃќгЃ®д»–гЃ®иїЅи·ЎжЉЂиЎ“г‚’дЅїз”ЁгЃ—гЃ¦гЃ„гЃѕгЃ™гЂ‚","nb_title":"г‚Їгѓѓг‚­гѓјгЃ®дЅїз”Ё","pc_fnct_text_1":"ж©џиѓЅжЂ§г‚Їгѓѓг‚­гѓј","pc_fnct_text_2":"гЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјгЃЇгЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ§г‚€г‚Љг‚«г‚№г‚їгѓћг‚¤г‚єгЃ•г‚ЊгЃџдЅ“йЁ“г‚’жЏђдѕ›гЃ™г‚‹гЃџг‚ЃгЂЃгЃЉг‚€гЃіеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€г‚’е€©з”ЁгЃ™г‚‹йљ›гЃ«иЎЊгЃЈгЃџйЃёжЉћг‚’иЁж†¶гЃ™г‚‹гЃџг‚ЃгЃ«дЅїз”ЁгЃ•г‚ЊгЃѕгЃ™гЂ‚","pc_fnct_text_3":"дѕ‹гЃ€гЃ°гЂЃиЁЄе•ЏиЂ…гЃ®иЁЂиЄћиЁ­е®љг‚’иЁж†¶гЃ—гЃџг‚ЉгЂЃгѓ­г‚°г‚¤гѓіжѓ…е ±г‚’иЁж†¶гЃ™г‚‹гЃџг‚ЃгЃ«гЂЃж©џиѓЅжЂ§г‚Їгѓѓг‚­гѓјг‚’дЅїз”ЁгЃ™г‚‹гЃ“гЃЁгЃЊгЃ‚г‚ЉгЃѕгЃ™гЂ‚","pc_minfo_text_1":"и©ізґ°жѓ…е ±","pc_minfo_text_2":"г‚Їгѓѓг‚­гѓјгЃ«й–ўгЃ™г‚‹ж–№й‡ќг‚„иЁЄе•ЏиЂ…гЃ®йЃёжЉћгЃ«й–ўйЂЈгЃ—гЃџгЃ”иіЄе•ЏгЃ«гЃ¤гЃ„гЃ¦гЃЇгЂЃеЅ“ж–№гЃѕгЃ§гЃЉе•ЏгЃ„еђ€г‚ЏгЃ›гЃЏгЃ гЃ•гЃ„гЂ‚","pc_minfo_text_3":"и©ігЃ—гЃЏгЃЇгЂЃ<a href=\'%s\' target=\'_blank\'>гѓ—гѓ©г‚¤гѓђг‚·гѓјгѓќгѓЄг‚·гѓј</a> г‚’гЃ”и¦§гЃЏгЃ гЃ•гЃ„гЂ‚","pc_save":"иЁ­е®љг‚’дїќе­","pc_sncssr_text_1":"г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ®е‹•дЅњгЃ«еї…и¦ЃдёЌеЏЇж¬ гЃЄг‚Їгѓѓг‚­гѓј","pc_sncssr_text_2":"гЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјгЃЇгЂЃиЁЄе•ЏиЂ…гЃЊеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€г‚’йЂљгЃгЃ¦е€©з”ЁеЏЇиѓЅгЃЄг‚µгѓјгѓ“г‚№г‚’жЏђдѕ›гЃ—гЃџг‚ЉгЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ®з‰№е®љгЃ®ж©џиѓЅг‚’е€©з”ЁгЃ—гЃџг‚ЉгЃ™г‚‹гЃџг‚ЃгЃ«дёЌеЏЇж¬ гЃЄг‚‚гЃ®гЃ§гЃ™гЂ‚","pc_sncssr_text_3":"гЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјг‚’гѓ–гѓ­гѓѓг‚ЇгЃ—гЃџе ґеђ€гЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ§гЃ®з‰№е®љгЃ®г‚µгѓјгѓ“г‚№г‚’жЏђдѕ›гЃ§гЃЌгЃѕгЃ›г‚“гЂ‚","pc_title":"г‚Їгѓѓг‚­гѓјиЁ­е®љг‚»гѓіг‚їгѓј","pc_trck_text_1":"гѓ€гѓ©гѓѓг‚­гѓіг‚°г‚Їгѓѓг‚­гѓј","pc_trck_text_2":"гЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјгЃЇгЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃёгЃ®гѓ€гѓ©гѓ•г‚Јгѓѓг‚Їг‚„иЁЄе•ЏиЂ…гЃЊгЃ©гЃ®г‚€гЃ†гЃ«еЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€г‚’е€©з”ЁгЃ—гЃ¦гЃ„г‚‹гЃ‹г‚’е€†жћђгЃ™г‚‹гЃџг‚ЃгЃ®жѓ…е ±г‚’еЏЋй›†гЃ™г‚‹гЃџг‚ЃгЃ«дЅїз”ЁгЃ•г‚ЊгЃѕгЃ™гЂ‚","pc_trck_text_3":"дѕ‹гЃ€гЃ°гЂЃгЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјгЃЇгЂЃиЁЄе•ЏиЂ…гЃЊеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ«ж»ћењЁгЃ—гЃџж™‚й–“г‚„иЁЄе•ЏгЃ—гЃџгѓљгѓјг‚ёгЃЄгЃ©г‚’иїЅи·ЎгЃ™г‚‹гЃ“гЃЁгЃЊгЃ‚г‚ЉгЂЃгЃ“г‚ЊгЃЇгЂЃиЁЄе•ЏиЂ…гЃ®гЃџг‚ЃгЃ«еЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ®е€©дѕїжЂ§еђ‘дёЉгЃ«еЅ№з«‹гЃ¦гЃѕгЃ™гЂ‚","pc_trck_text_4":"гЃ“г‚Њг‚‰гЃ®гѓ€гѓ©гѓѓг‚­гѓіг‚°гЃЉг‚€гЃігѓ‘гѓ•г‚©гѓјгѓћгѓіг‚№г‚Їгѓѓг‚­гѓјгЃ«г‚€гЃЈгЃ¦еЏЋй›†гЃ•г‚ЊгЃџжѓ…е ±гЃЇгЂЃз‰№е®љгЃ®еЂ‹дєєг‚’з‰№е®љгЃ™г‚‹гЃ“гЃЁгЃЇгЃ‚г‚ЉгЃѕгЃ›г‚“гЂ‚","pc_trgt_text_1":"г‚їгѓјг‚Ігѓ†г‚Јгѓіг‚°гЃЉг‚€гЃіеєѓе‘Љз”Ёг‚Їгѓѓг‚­гѓј","pc_trgt_text_2":"гЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјгЃЇгЂЃиЁЄе•ЏиЂ…гЃ®й–Іи¦§зї’ж…ЈгЃ«еџєгЃҐгЃ„гЃ¦гЂЃиЁЄе•ЏиЂ…гЃЊи€€е‘іг‚’жЊЃгЃЎгЃќгЃ†гЃЄеєѓе‘Љг‚’иЎЁз¤єгЃ™г‚‹гЃџг‚ЃгЃ«дЅїз”ЁгЃ•г‚ЊгЃѕгЃ™гЂ‚","pc_trgt_text_3":"гЃ“г‚Њг‚‰гЃ®г‚Їгѓѓг‚­гѓјгЃЇгЂЃг‚ігѓігѓ†гѓігѓ„гѓ—гѓ­гѓђг‚¤гѓЂгѓјгЃЉг‚€гЃі/гЃѕгЃџгЃЇеєѓе‘Љгѓ—гѓ­гѓђг‚¤гѓЂгѓјгЃ«г‚€гЃЈгЃ¦жЏђдѕ›гЃ•г‚ЊгЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ‹г‚‰еЏЋй›†гЃ—гЃџжѓ…е ±гЃЁгЂЃгЃќгЃ®гѓЌгѓѓгѓ€гѓЇгѓјг‚ЇдёЉгЃ§гЃ®иЁЄе•ЏиЂ…гЃ®г‚¦г‚§гѓ–гѓ–гѓ©г‚¦г‚¶гЃ®жґ»е‹•гЃ«й–ўйЂЈгЃ—гЃ¦з‹¬и‡ЄгЃ«еЏЋй›†гЃ—гЃџд»–гЃ®жѓ…е ±гЃЁг‚’зµ„гЃїеђ€г‚ЏгЃ›г‚‹гЃ“гЃЁгЃЊгЃ‚г‚ЉгЃѕгЃ™гЂ‚","pc_trgt_text_4":"иЁЄе•ЏиЂ…гЃЊгЃ“г‚Њг‚‰гЃ®г‚їгѓјг‚Ігѓ†г‚Јгѓіг‚°г‚Їгѓѓг‚­гѓјг‚„еєѓе‘Љз”Ёг‚Їгѓѓг‚­гѓјг‚’е‰Љй™¤гЃѕгЃџгЃЇз„ЎеЉ№г‚’йЃёжЉћгЃ—гЃџе ґеђ€гЃ§г‚‚гЂЃеєѓе‘ЉгЃЇиЎЁз¤єгЃ•г‚ЊгЃѕгЃ™гЃЊгЂЃиЁЄе•ЏиЂ…гЃ«й–ўйЂЈгЃ—гЃџг‚‚гЃ®гЃ§гЃЇгЃЄгЃ„еЏЇиѓЅжЂ§гЃЊгЃ‚г‚ЉгЃѕгЃ™гЂ‚","pc_yprivacy_text_1":"гЃЉе®ўж§гЃ®гѓ—гѓ©г‚¤гѓђг‚·гѓјг‚’е°Љй‡ЌгЃ—гЃѕгЃ™","pc_yprivacy_text_2":"г‚Їгѓѓг‚­гѓјгЃЁгЃЇгЂЃиЁЄе•ЏиЂ…гЃЊг‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ«г‚ўг‚Їг‚»г‚№гЃ—гЃџйљ›гЃ«иЁЄе•ЏиЂ…гЃ®г‚ігѓігѓ”гѓҐгѓјг‚їгЃ«дїќе­гЃ•г‚Њг‚‹йќћеёёгЃ«е°ЏгЃ•гЃЄгѓ†г‚­г‚№гѓ€гѓ•г‚Ўг‚¤гѓ«гЃ§гЃ™гЂ‚еЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃЇгЂЃгЃ•гЃѕгЃ–гЃѕгЃЄз›®зљ„гЃ§г‚Їгѓѓг‚­гѓјг‚’дЅїз”ЁгЃ—гЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€гЃ§гЃ®иЁЄе•ЏиЂ…гЃ®г‚Єгѓігѓ©г‚¤гѓіе€©дѕїжЂ§г‚’еђ‘дёЉгЃ•гЃ›гЃ¦гЃ„гЃѕгЃ™гЂ‚пј€дѕ‹гЃ€гЃ°гЂЃиЁЄе•ЏиЂ…гЃ®г‚ўг‚«г‚¦гѓігѓ€гЃ®гѓ­г‚°г‚¤гѓіжѓ…е ±г‚’иЁж†¶гЃ™г‚‹гЃџг‚ЃгЃЄгЃ©гЂ‚пј‰","pc_yprivacy_text_3":"иЁЄе•ЏиЂ…гЃЇгЂЃиЁ­е®љг‚’е¤‰ж›ґгЃ—гЃ¦гЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€г‚’й–Іи¦§дё­гЃ®г‚ігѓігѓ”гѓҐгѓјг‚їгЃ«дїќе­гЃ•г‚Њг‚‹з‰№е®љгЃ®зЁ®йЎћгЃ®г‚Їгѓѓг‚­гѓјг‚’ж‹’еђ¦гЃ™г‚‹гЃ“гЃЁгЃЊгЃ§гЃЌгЃѕгЃ™гЂ‚гЃѕгЃџгЂЃгЃ™гЃ§гЃ«иЁЄе•ЏиЂ…гЃ®г‚ігѓігѓ”гѓҐгѓјг‚їгЃ«дїќе­гЃ•г‚ЊгЃ¦гЃ„г‚‹г‚Їгѓѓг‚­гѓјг‚’е‰Љй™¤гЃ™г‚‹гЃ“гЃЁг‚‚гЃ§гЃЌгЃѕгЃ™гЃЊгЂЃг‚Їгѓѓг‚­гѓјг‚’е‰Љй™¤гЃ™г‚‹гЃЁгЂЃеЅ“г‚¦г‚§гѓ–г‚µг‚¤гѓ€ж©џиѓЅгЃ®дёЂйѓЁгЃЊе€©з”ЁгЃ§гЃЌгЃЄгЃЏгЃЄг‚‹еЏЇиѓЅжЂ§гЃЊгЃ‚г‚ЉгЃѕгЃ™гЃ®гЃ§гЂЃгЃ”жіЁж„ЏгЃЏгЃ гЃ•гЃ„гЂ‚","pc_yprivacy_title":"гѓ—гѓ©г‚¤гѓђг‚·гѓј","privacy_policy":"<a href=\'%s\' target=\'_blank\'>гѓ—гѓ©г‚¤гѓђг‚·гѓјгѓќгѓЄг‚·гѓј</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"ШЄШєЩЉЩЉШ± ШЄЩЃШ¶ЩЉЩ„Ш§ШЄЩЉ","always_active":"Щ…ЩЃШ№Щ„ ШЇШ§Ш¦Щ…Щ‹Ш§","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"ШєЩЉШ± Щ…ЩЃШ№Щ„","nb_agree":"Щ…Щ€Ш§ЩЃЩ‚","nb_changep":"ШЄШєЩЉЩЉШ± ШЄЩЃШ¶ЩЉЩ„Ш§ШЄЩЉ","nb_ok":"ЩЃЩ‡Щ…ШЄ","nb_reject":"ШЈШ±ЩЃШ¶","nb_text":"Щ†Ш­Щ† Щ†ШіШЄШ®ШЇЩ… Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ€ШЄЩ‚Щ†ЩЉШ§ШЄ Ш§Щ„ШЄШЄШЁШ№ Ш§Щ„ШЈШ®Ш±Щ‰ Щ„ШЄШ­ШіЩЉЩ† ШЄШ¬Ш±ШЁШ© Ш§Щ„ШЄШµЩЃШ­ Ш§Щ„Ш®Ш§ШµШ© ШЁЩѓ Ш№Щ„Щ‰ Щ…Щ€Щ‚Ш№Щ†Ш§ Ш§Щ„ШҐЩ„ЩѓШЄШ±Щ€Щ†ЩЉ ШЊ Щ€Щ„ШҐШёЩ‡Ш§Ш± Ш§Щ„Щ…Ш­ШЄЩ€Щ‰ Ш§Щ„Щ…Ш®ШµШµ Щ€Ш§Щ„ШҐШ№Щ„Ш§Щ†Ш§ШЄ Ш§Щ„Щ…ШіШЄЩ‡ШЇЩЃШ© Щ„Щѓ ШЊ Щ€ШЄШ­Щ„ЩЉЩ„ Ш­Ш±ЩѓШ© Ш§Щ„Щ…Ш±Щ€Ш± Ш№Щ„Щ‰ Щ…Щ€Щ‚Ш№Щ†Ш§ ШЊ Щ€ЩЃЩ‡Щ… Щ…Щ† ШЈЩЉЩ† ЩЉШЈШЄЩЉ ШІЩ€Ш§Ш±Щ†Ш§.","nb_title":"Щ†Ш­Щ†ЩЏ Щ†ШіШЄШ®ШЇЩ… Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш·","pc_fnct_text_1":"Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Ш§Щ„Щ€ШёЩЉЩЃЩЉШ©","pc_fnct_text_2":"ШЄЩЏШіШЄШ®ШЇЩ… Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ Щ„ШЄШІЩ€ЩЉШЇЩѓ ШЁШЄШ¬Ш±ШЁШ© ШЈЩѓШ«Ш± ШЄШ®ШµЩЉШµЩ‹Ш§ Ш№Щ„Щ‰ Щ…Щ€Щ‚Ш№Щ†Ш§ Ш§Щ„ШҐЩ„ЩѓШЄШ±Щ€Щ†ЩЉ Щ€Щ„ШЄШ°ЩѓШ± Ш§Щ„Ш®ЩЉШ§Ш±Ш§ШЄ Ш§Щ„ШЄЩЉ ШЄШЄШ®Ш°Щ‡Ш§ Ш№Щ†ШЇ Ш§ШіШЄШ®ШЇШ§Щ…Щѓ Щ„Щ…Щ€Щ‚Ш№Щ†Ш§.","pc_fnct_text_3":"Ш№Щ„Щ‰ ШіШЁЩЉЩ„ Ш§Щ„Щ…Ш«Ш§Щ„ ШЊ Щ‚ШЇ Щ†ШіШЄШ®ШЇЩ… Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Ш§Щ„Щ€ШёЩЉЩЃЩЉШ© Щ„ШЄШ°ЩѓШ± ШЄЩЃШ¶ЩЉЩ„Ш§ШЄ Ш§Щ„Щ„ШєШ© Ш§Щ„Ш®Ш§ШµШ© ШЁЩѓ ШЈЩ€ ШЄШ°ЩѓШ± ШЄЩЃШ§ШµЩЉЩ„ ШЄШіШ¬ЩЉЩ„ Ш§Щ„ШЇШ®Щ€Щ„ Ш§Щ„Ш®Ш§ШµШ© ШЁЩѓ.","pc_minfo_text_1":"Щ…Ш№Щ„Щ€Щ…Ш§ШЄ ШЈЩѓШ«Ш±.","pc_minfo_text_2":"Щ„ШЈЩЉ Ш§ШіШЄЩЃШіШ§Ш±Ш§ШЄ ШЄШЄШ№Щ„Щ‚ ШЁШіЩЉШ§ШіШЄЩ†Ш§ Ш§Щ„Ш®Ш§ШµШ© ШЁЩ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· ШЊ Щ€Ш®ЩЉШ§Ш±Ш§ШЄЩѓШЊ  ЩЉШ±Ш¬Щ‰ Ш§Щ„ШЄЩ€Ш§ШµЩ„ Щ…Ш№Щ†Ш§.","pc_minfo_text_3":"<a href=\'%s\' target=\'_blank\'>\\nШ§Щ„Ш®Ш§ШµШ© ШЁЩ†Ш§ Щ„Щ…Ш№Ш±ЩЃШ© Ш§Щ„Щ…ШІЩЉШЇ ШЊ ЩЉШ±Ш¬Щ‰ ШІЩЉШ§Ш±Ш©ШіЩЉШ§ШіШ© Ш§Щ„Ш®ШµЩ€ШµЩЉШ© .\\n</a>","pc_save":"Ш­ЩЃШё ШЄЩЃШ¶ЩЉЩ„Ш§ШЄЩЉ","pc_sncssr_text_1":"Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Ш§Щ„Ш¶Ш±Щ€Ш±ЩЉШ© Щ„Щ„ШєШ§ЩЉШ©","pc_sncssr_text_2":"ШЄШ№ШЇ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ Ш¶Ш±Щ€Ш±ЩЉШ© Щ„ШЄШІЩ€ЩЉШЇЩѓ ШЁШ§Щ„Ш®ШЇЩ…Ш§ШЄ Ш§Щ„Щ…ШЄШ§Ш­Ш© Ш№ШЁШ± Щ…Щ€Щ‚Ш№Щ†Ш§ Ш№Щ„Щ‰ Ш§Щ„Щ€ЩЉШЁ Щ€Щ„ШЄЩ…ЩѓЩЉЩ†Щѓ Щ…Щ† Ш§ШіШЄШ®ШЇШ§Щ… Щ…ЩЉШІШ§ШЄ Щ…Ш№ЩЉЩ†Ш© ЩЃЩЉ Щ…Щ€Щ‚Ш№Щ†Ш§ .","pc_sncssr_text_3":"ШЁШЇЩ€Щ† Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ ШЊ Щ„Ш§ ЩЉЩ…ЩѓЩ†Щ†Ш§ ШЄЩ‚ШЇЩЉЩ… Ш®ШЇЩ…Ш§ШЄ Щ…Ш№ЩЉЩ†Ш© Щ„Щѓ Ш№Щ„Щ‰ Щ…Щ€Щ‚Ш№Щ†Ш§.","pc_title":"Щ…Ш±ЩѓШІ ШЄЩЃШ¶ЩЉЩ„Ш§ШЄ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш·","pc_trck_text_1":"Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ„Щ„ШЄШЄШЁШ№ Щ€Ш§Щ„ШЈШЇШ§ШЎ","pc_trck_text_2":"\\nШЄЩЏШіШЄШ®ШЇЩ… Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ Щ„Ш¬Щ…Ш№ Ш§Щ„Щ…Ш№Щ„Щ€Щ…Ш§ШЄ Щ„ШЄШ­Щ„ЩЉЩ„ Ш­Ш±ЩѓШ© Ш§Щ„Щ…Ш±Щ€Ш± ШҐЩ„Щ‰ Щ…Щ€Щ‚Ш№Щ†Ш§ Ш§Щ„ШҐЩ„ЩѓШЄШ±Щ€Щ†ЩЉ Щ€ЩѓЩЉЩЃЩЉШ© Ш§ШіШЄШ®ШЇШ§Щ… Ш§Щ„ШІЩ€Ш§Ш± Щ„Щ…Щ€Щ‚Ш№Щ†Ш§.","pc_trck_text_3":"\\nШ№Щ„Щ‰ ШіШЁЩЉЩ„ Ш§Щ„Щ…Ш«Ш§Щ„ ШЊ Щ‚ШЇ ШЄШЄШ№Щ‚ШЁ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ ШЈШґЩЉШ§ШЎ Щ…Ш«Щ„ Ш§Щ„Щ…ШЇШ© Ш§Щ„ШЄЩЉ ШЄЩ‚Ш¶ЩЉЩ‡Ш§ Ш№Щ„Щ‰ Щ…Щ€Щ‚Ш№ Ш§Щ„Щ€ЩЉШЁ ШЈЩ€ Ш§Щ„ШµЩЃШ­Ш§ШЄ Ш§Щ„ШЄЩЉ ШЄШІЩ€Ш±Щ‡Ш§ Щ…Щ…Ш§ ЩЉШіШ§Ш№ШЇЩ†Ш§ Ш№Щ„Щ‰ ЩЃЩ‡Щ… ЩѓЩЉЩЃ ЩЉЩ…ЩѓЩ†Щ†Ш§ ШЄШ­ШіЩЉЩ† Щ…Щ€Щ‚Ш№Щ†Ш§ Ш№Щ„Щ‰ Ш§Щ„Щ€ЩЉШЁ Щ…Щ† ШЈШ¬Щ„Щѓ.","pc_trck_text_4":"\\nШ§Щ„Щ…Ш№Щ„Щ€Щ…Ш§ШЄ Ш§Щ„ШЄЩЉ ЩЉШЄЩ… Ш¬Щ…Ш№Щ‡Ш§ Щ…Щ† Ш®Щ„Ш§Щ„ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Ш§Щ„Ш®Ш§ШµШ© ШЁШ§Щ„ШЄШЄШЁШ№ Щ€Ш§Щ„ШЈШЇШ§ШЎ Щ‡Ш°Щ‡ Щ„Ш§ ШЄШ­ШЇШЇ ШЈЩЉ ШІШ§Ш¦Ш± ЩЃШ±ШЇЩЉ.\\n","pc_trgt_text_1":"Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Ш§Щ„Ш®Ш§ШµШ© ШЁШ§Щ„Ш§ШіШЄЩ‡ШЇШ§ЩЃ Щ€Ш§Щ„ШҐШ№Щ„Ш§Щ†","pc_trgt_text_2":"ШЄЩЏШіШЄШ®ШЇЩ… Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ Щ„ШҐШёЩ‡Ш§Ш± Ш§Щ„ШҐШ№Щ„Ш§Щ†Ш§ШЄ Ш§Щ„ШЄЩЉ Щ…Щ† Ш§Щ„Щ…Ш­ШЄЩ…Щ„ ШЈЩ† ШЄЩ‡Щ…Щѓ ШЁЩ†Ш§ШЎЩ‹ Ш№Щ„Щ‰ Ш№Ш§ШЇШ§ШЄЩѓ ЩЃЩЉ Ш§Щ„ШЄШµЩЃШ­.","pc_trgt_text_3":"\\nЩ‚ШЇ ШЄШЇЩ…Ш¬ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‡Ш°Щ‡ ШЊ ЩѓЩ…Ш§ ЩЉЩ‚ШЇЩ…Щ‡Ш§ Ш§Щ„Щ…Ш­ШЄЩ€Щ‰ Щ€ / ШЈЩ€ Щ…Щ€ЩЃШ±Щ€ Ш§Щ„ШҐШ№Щ„Ш§Щ†Ш§ШЄ Щ„ШЇЩЉЩ†Ш§ ШЊ Ш§Щ„Щ…Ш№Щ„Щ€Щ…Ш§ШЄ Ш§Щ„ШЄЩЉ Ш¬Щ…Ш№Щ€Щ‡Ш§ Щ…Щ† Щ…Щ€Щ‚Ш№Щ†Ш§ Ш§Щ„ШҐЩ„ЩѓШЄШ±Щ€Щ†ЩЉ Щ…Ш№ Ш§Щ„Щ…Ш№Щ„Щ€Щ…Ш§ШЄ Ш§Щ„ШЈШ®Ш±Щ‰ Ш§Щ„ШЄЩЉ Ш¬Щ…Ш№Щ€Щ‡Ш§ ШЁШґЩѓЩ„ Щ…ШіШЄЩ‚Щ„ ЩЃЩЉЩ…Ш§ ЩЉШЄШ№Щ„Щ‚ ШЁШЈЩ†ШґШ·Ш© Щ…ШЄШµЩЃШ­ Ш§Щ„Щ€ЩЉШЁ Ш§Щ„Ш®Ш§Шµ ШЁЩѓ Ш№ШЁШ± ШґШЁЩѓШ© Щ…Щ€Ш§Щ‚Ш№Щ‡Щ… Ш§Щ„ШҐЩ„ЩѓШЄШ±Щ€Щ†ЩЉШ©.\\n","pc_trgt_text_4":"ШҐШ°Ш§ Ш§Ш®ШЄШ±ШЄ ШҐШІШ§Щ„Ш© ШЈЩ€ ШЄШ№Ш·ЩЉЩ„ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Ш§Щ„Ш®Ш§ШµШ© ШЁШ§Щ„Ш§ШіШЄЩ‡ШЇШ§ЩЃ ШЈЩ€ Ш§Щ„ШҐШ№Щ„Ш§Щ†Ш§ШЄ ШЊ ЩЃШіШЄШёЩ„ ШЄШґШ§Щ‡ШЇ ШҐШ№Щ„Ш§Щ†Ш§ШЄ Щ€Щ„ЩѓЩ†Щ‡Ш§ Щ‚ШЇ Щ„Ш§ ШЄЩѓЩ€Щ† Ш°Ш§ШЄ ШµЩ„Ш© ШЁЩѓ.","pc_yprivacy_text_1":"Ш®ШµЩ€ШµЩЉШЄЩѓ Щ…Щ‡Щ…Ш© ШЁШ§Щ„Щ†ШіШЁШ© Щ„Щ†Ш§","pc_yprivacy_text_2":"Щ…Щ† Ш§Щ„ШЈШєШ±Ш§Ш¶ Щ€Щ„ШЄШ№ШІЩЉШІ ШЄШ¬Ш±ШЁШЄЩѓ Ш№ШЁШ± Ш§Щ„ШҐЩ†ШЄШ±Щ†ШЄ Ш№Щ„Щ‰ Щ…Щ€Щ‚Ш№Щ†Ш§ (Ш№Щ„Щ‰ ШіШЁЩЉЩ„ Ш§Щ„Щ…Ш«Ш§Щ„ ШЊ Щ„ШЄШ°ЩѓШ± ШЄЩЃШ§ШµЩЉЩ„ ШЄШіШ¬ЩЉЩ„ Ш§Щ„ШЇШ®Щ€Щ„ ШҐЩ„Щ‰ Ш­ШіШ§ШЁЩѓ).","pc_yprivacy_text_3":"ЩЉЩ…ЩѓЩ†Щѓ ШЄШєЩЉЩЉШ± ШЄЩЃШ¶ЩЉЩ„Ш§ШЄЩѓ Щ€Ш±ЩЃШ¶ ШЈЩ†Щ€Ш§Ш№ Щ…Ш№ЩЉЩ†Ш© Щ…Щ† Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ„ЩЉШЄЩ… ШЄШ®ШІЩЉЩ†Щ‡Ш§ Ш№Щ„Щ‰ Ш¬Щ‡Ш§ШІ Ш§Щ„ЩѓЩ…ШЁЩЉЩ€ШЄШ± Ш§Щ„Ш®Ш§Шµ ШЁЩѓ ШЈШ«Щ†Ш§ШЎ ШЄШµЩЃШ­ Щ…Щ€Щ‚Ш№Щ†Ш§ Ш№Щ„Щ‰ Ш§Щ„Щ€ЩЉШЁ.  ЩЉЩ…ЩѓЩ†Щѓ ШЈЩЉШ¶Щ‹Ш§ ШҐШІШ§Щ„Ш© ШЈЩЉ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Ш±ШЄШЁШ§Ш· Щ…Ш®ШІЩ†Ш© ШЁШ§Щ„ЩЃШ№Щ„ Ш№Щ„Щ‰ Ш¬Щ‡Ш§ШІ Ш§Щ„ЩѓЩ…ШЁЩЉЩ€ШЄШ± Ш§Щ„Ш®Ш§Шµ ШЁЩѓ ШЊ Щ€Щ„ЩѓЩ† Ш¶Ш№ ЩЃЩЉ Ш§Ш№ШЄШЁШ§Ш±Щѓ ШЈЩ† Ш­Ш°ЩЃ Щ…Щ„ЩЃШ§ШЄ ШЄШ№Ш±ЩЉЩЃ Ш§Щ„Ш§Ш±ШЄШЁШ§Ш· Щ‚ШЇ ЩЉЩ…Щ†Ш№Щѓ Щ…Щ† Ш§ШіШЄШ®ШЇШ§Щ… ШЈШ¬ШІШ§ШЎ Щ…Щ† Щ…Щ€Щ‚Ш№Щ†Ш§.","pc_yprivacy_title":"Ш®ШµЩ€ШµЩЉШЄЩѓ","privacy_policy":"<a href=\'%s\' target=\'_blank\'>\\nШ©ШіЩЉШ§ШіШ© Ш§Щ„Ш®ШµЩ€ШµЩЉШ©\\n</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Etkin","always_active":"Her zaman etkin","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"Etkin deДџil","nb_agree":"Kabul et","nb_changep":"Tercihleri deДџiЕџtir","nb_ok":"Tamam","nb_reject":"Reddet","nb_text":"Web sitemizde gezinme deneyiminizi geliЕџtirmek, size kiЕџiselleЕџtirilmiЕџ iГ§erik ve hedefli reklamlar gГ¶stermek, web sitesi trafiДџimizi analiz etmek ve ziyaretГ§ilerimizin nereden geldiДџini anlamak iГ§in Г§erezleri ve diДџer izleme teknolojilerini kullanД±yoruz.","nb_title":"Г‡erezleri kullanД±yoruz","pc_fnct_text_1":"Д°Еџlevsellik Г§erezleri","pc_fnct_text_2":"Bu Г§erezler, web sitemizde size daha kiЕџiselleЕџtirilmiЕџ bir deneyim saДџlamak ve web sitemizi kullanД±rken yaptД±ДџД±nД±z seГ§imleri hatД±rlamak iГ§in kullanД±lД±r.","pc_fnct_text_3":"Г–rneДџin, dil tercihlerinizi veya oturum aГ§ma bilgilerinizi hatД±rlamak iГ§in iЕџlevsellik tanД±mlama bilgilerini kullanabiliriz.","pc_minfo_text_1":"Daha fazla bilgi","pc_minfo_text_2":"Г‡erezlere iliЕџkin politikamД±z ve seГ§imlerinizle ilgili herhangi bir sorunuz iГ§in lГјtfen bizimle iletiЕџime geГ§in","pc_minfo_text_3":"Daha fazlasД±nД± Г¶Дџrenmek iГ§in lГјtfen <a href=\'%s\' target=\'_blank\'>Gizlilik PolitikasД±</a> ziyaret edin.","pc_save":"Tercihleri Kaydet","pc_sncssr_text_1":"Kesinlikle gerekli Г§erezler","pc_sncssr_text_2":"Bu Г§erezler, size web sitemiz aracД±lД±ДџД±yla sunulan hizmetleri saДџlamak ve web sitemizin belirli Г¶zelliklerini kullanmanД±zД± saДџlamak iГ§in gereklidir.","pc_sncssr_text_3":"Bu Г§erezler olmadan, web sitemizde size belirli hizmetleri saДџlayamayД±z.","pc_title":"Г‡erez Tercihleri Merkezi","pc_trck_text_1":"Д°zleme ve performans Г§erezleri","pc_trck_text_2":"Bu Г§erezler, web sitemize gelen trafiДџi ve ziyaretГ§ilerin web sitemizi nasД±l kullandД±ДџД±nД± analiz etmek iГ§in bilgi toplamak amacД±yla kullanД±lД±r.","pc_trck_text_3":"Г–rneДџin, Г§erezler, web sitesinde ne kadar zaman geГ§irdiДџiniz veya ziyaret ettiДџiniz sayfalar gibi Еџeyleri izleyebilir ve bu da web sitemizi sizin iГ§in nasД±l iyileЕџtirebileceДџimizi anlamamД±za yardД±mcД± olur.","pc_trck_text_4":"Bu izleme ve performans Г§erezleri aracД±lД±ДџД±yla toplanan bilgiler anonim olup herhangi bir bireysel ziyaretГ§iyi tanД±mlamaz.","pc_trgt_text_1":"Hedefleme ve reklam Г§erezleri","pc_trgt_text_2":"Bu Г§erezler, arama/gezinme alД±ЕџkanlД±klarД±nД±za gГ¶re ilginizi Г§ekebilecek reklamlarД± gГ¶stermek iГ§in kullanД±lД±r.","pc_trgt_text_3":"Bu Г§erezler, iГ§erik ve/veya reklam saДџlayД±cД±larД±mД±z tarafД±ndan, web sitemizden topladД±klarД± bilgileri, web tarayД±cД±nД±zД±n kendi web siteleri aДџlarД±ndaki faaliyetleriyle ilgili olarak baДџД±msД±z olarak topladД±klarД± diДџer bilgilerle birleЕџtirilebilir.","pc_trgt_text_4":"Bu hedefleme veya reklam Г§erezlerini kaldД±rmayД± veya devre dД±ЕџД± bД±rakmayД± seГ§erseniz, reklamlarД± gГ¶rmeye devam edersiniz, ancak bunlar sizinle alakalД± olmayabilir.","pc_yprivacy_text_1":"GizliliДџiniz bizim iГ§in Г¶nemlidir","pc_yprivacy_text_2":"Г‡erezler, bir web sitesini ziyaret ettiДџinizde bilgisayarД±nД±zda depolanan Г§ok kГјГ§Гјk metin dosyalarД±dД±r. Г‡erezleri Г§eЕџitli amaГ§larla ve web sitemizdeki Г§evrimiГ§i deneyiminizi geliЕџtirmek iГ§in (Г¶rneДџin, hesap giriЕџ bilgilerinizi hatД±rlamak iГ§in) kullanД±yoruz.","pc_yprivacy_text_3":"Web sitemizde gezinirken tercihlerinizi deДџiЕџtirebilir ve bilgisayarД±nД±zda saklanacak belirli Г§erez tГјrlerini reddedebilirsiniz. AyrД±ca, bilgisayarД±nД±zda depolanmД±Еџ olan Г§erezleri de kaldД±rabilirsiniz, ancak Г§erezleri silmenin web sitemizin bГ¶lГјmlerini kullanmanД±zД± engelleyebileceДџini unutmayД±n.","pc_yprivacy_title":"GizliliДџiniz","privacy_policy":"<a href=\'%s\' target=\'_blank\'>Gizlilik PolitikasД±</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"е•џз”Ё","always_active":"ж°ёйЃ е•џз”Ё","impressum":"<a href=\'%s\' target=\'_blank\'>Impressum</a>","inactive":"еЃњз”Ё","nb_agree":"ж€‘еђЊж„Џ","nb_changep":"ж›ґж”№ж€‘зљ„еЃЏеҐЅ","nb_ok":"зўєе®љ","nb_reject":"ж€‘ж‹’зµ•","nb_text":"ж€‘еЂ‘дЅїз”Ёcookiesе’Ње…¶д»–иїЅи№¤жЉЂиЎ“дѕ†ж”№е–„ж‚ЁењЁж€‘еЂ‘з¶Із«™дёЉзљ„зЂЏи¦Ѕй«”й©—пјЊе°Ќж‚ЁйЎЇз¤єеЂ‹жЂ§еЊ–зљ„е…§е®№е’Њжњ‰й‡ќе°ЌжЂ§зљ„е»Је‘ЉпјЊе€†жћђж€‘еЂ‘зљ„з¶Із«™жµЃй‡ЏпјЊдё¦дє†и§Јж€‘еЂ‘зљ„иЁЄе®ўдѕ†и‡Єе“ЄиЈЎгЂ‚","nb_title":"ж€‘еЂ‘дЅїз”Ёcookies","pc_fnct_text_1":"еЉџиѓЅжЂ§cookies","pc_fnct_text_2":"йЂ™дє›cookiesз”Ёж–јењЁж€‘еЂ‘зљ„з¶Із«™дёЉз‚єж‚ЁжЏђдѕ›ж›ґеЉ еЂ‹дєєеЊ–зљ„й«”й©—пјЊдё¦иЁдЅЏж‚ЁењЁдЅїз”Ёж€‘еЂ‘з¶Із«™ж™‚еЃље‡єзљ„йЃёж“‡гЂ‚","pc_fnct_text_3":"дѕ‹е¦‚пјЊж€‘еЂ‘еЏЇиѓЅдЅїз”ЁеЉџиѓЅжЂ§cookiesдѕ†иЁдЅЏж‚Ёзљ„иЄћиЁЂеЃЏеҐЅж€–иЁдЅЏж‚Ёзљ„з™»е…Ґиі‡иЁЉгЂ‚","pc_minfo_text_1":"ж›ґе¤љиі‡иЁЉ","pc_minfo_text_2":"е¦‚жћње°Ќж€‘еЂ‘зљ„cookiesж”їз­–ж€–ж‚Ёзљ„йЃёж“‡жњ‰д»»дЅ•з–‘е•ЏпјЊи«‹иЃЇз№«ж€‘еЂ‘гЂ‚","pc_minfo_text_3":"жѓідє†и§Јж›ґе¤љиі‡иЁЉпјЊи«‹е‰ЌеѕЂж€‘еЂ‘зљ„<a href=\'%s\' target=\'_blank\'>йљ±з§Ѓж¬Љж”їз­–</a>.","pc_save":"е„Іе­ж€‘зљ„еЃЏеҐЅ","pc_sncssr_text_1":"еї…и¦Ѓзљ„cookies","pc_sncssr_text_2":"йЂ™дє›cookiesе°Ќж–јеђ‘ж‚ЁжЏђдѕ›йЂЏйЃЋж€‘еЂ‘з¶Із«™зљ„жњЌе‹™д»ҐеЏЉдЅїж‚ЁиѓЅе¤ дЅїз”Ёж€‘еЂ‘з¶Із«™зљ„жџђдє›еЉџиѓЅжЇдёЌеЏЇж€–зјєзљ„гЂ‚","pc_sncssr_text_3":"жІ’жњ‰йЂ™дє›cookiesпјЊж€‘еЂ‘е°±дёЌиѓЅењЁж€‘еЂ‘зљ„з¶Із«™дёЉз‚єж‚ЁжЏђдѕ›жџђдє›жњЌе‹™гЂ‚","pc_title":"CookiesеЃЏеҐЅдё­еїѓ","pc_trck_text_1":"иїЅи№¤cookies","pc_trck_text_2":"йЂ™дє›cookiesз”Ёж–јж”¶й›†иі‡иЁЉпјЊд»Ґе€†жћђж€‘еЂ‘з¶Із«™зљ„жµЃй‡Џд»ҐеЏЉиЁЄе®ўе¦‚дЅ•дЅїз”Ёж€‘еЂ‘зљ„з¶Із«™гЂ‚","pc_trck_text_3":"дѕ‹е¦‚пјЊйЂ™дє›cookiesеЏЇиѓЅжњѓи·џиїЅи№¤е¦‚ж‚ЁењЁз¶Із«™дёЉиЉ±иІ»зљ„ж™‚й–“ж€–ж‚ЁйЂ иЁЄзљ„й ЃйќўпјЊйЂ™жњ‰еЉ©ж–јж€‘еЂ‘дє†и§Је¦‚дЅ•з‚єж‚Ёж”№йЂІж€‘еЂ‘зљ„з¶Із«™гЂ‚","pc_trck_text_4":"йЂЏйЃЋйЂ™дє›иїЅи№¤е’ЊжЂ§иѓЅcookiesж”¶й›†зљ„иі‡иЁЉдёЌжњѓи­е€Ґд»»дЅ•еЂ‹дєєиЁЄе®ўгЂ‚","pc_trgt_text_1":"е®љдЅЌе’Ње»Је‘Љcookies","pc_trgt_text_2":"йЂ™дє›cookiesиў«з”Ёдѕ†ж №ж“љж‚Ёзљ„зЂЏи¦Ѕзї’ж…ЈйЎЇз¤єж‚ЁеЏЇиѓЅж„џи€€и¶Јзљ„е»Је‘ЉгЂ‚","pc_trgt_text_3":"з”±ж€‘еЂ‘зљ„е…§е®№ж€–е»Је‘Љдѕ›ж‡‰е•†жЏђдѕ›зљ„йЂ™дє›cookiesпјЊеЏЇиѓЅжњѓе°‡д»–еЂ‘еѕћж€‘еЂ‘зљ„з¶Із«™дёЉж”¶й›†зљ„иі‡иЁЉе’Њд»–еЂ‘зЌЁз«‹ж”¶й›†зљ„и€‡ж‚Ёзљ„зЂЏи¦Ѕе™ЁењЁе…¶з¶Із«™дё­зљ„жґ»е‹•жњ‰й—њзљ„е…¶д»–иі‡иЁЉзµђеђ€иµ·дѕ†гЂ‚","pc_trgt_text_4":"е¦‚жћњж‚ЁйЃёж“‡е€Єй™¤ж€–з¦Ѓз”ЁйЂ™дє›е®љдЅЌж€–е»Је‘ЉcookiesпјЊж‚Ёд»Ќз„¶жњѓзњ‹е€°е»Је‘ЉпјЊдЅ†е®ѓеЂ‘еЏЇиѓЅи€‡ж‚Ёз„Ўй—њгЂ‚","pc_yprivacy_text_1":"ж‚Ёзљ„йљ±з§Ѓе°Ќж€‘еЂ‘еѕ€й‡Ќи¦Ѓ","pc_yprivacy_text_2":"CookiesжЇйќћеёёе°Џзљ„ж–‡жњ¬ж–‡д»¶пјЊз•¶ж‚ЁйЂ иЁЄз¶Із«™ж™‚е­е„ІењЁж‚Ёзљ„иЈќзЅ®дёЉгЂ‚ж€‘еЂ‘е°‡cookiesз”Ёж–јеђ„зЁ®з›®зљ„пјЊдё¦жЏђй«ж‚ЁењЁж€‘еЂ‘з¶Із«™зљ„дЅїз”Ёй«”й©—пј€дѕ‹е¦‚пјЊиЁдЅЏж‚Ёеёіи™џзљ„з™»е…Ґиі‡иЁЉпј‰гЂ‚","pc_yprivacy_text_3":"ењЁзЂЏи¦Ѕж€‘еЂ‘зљ„з¶Із«™ж™‚пјЊж‚ЁеЏЇд»Ґж”№и®Љж‚Ёзљ„еЃЏеҐЅпјЊж‹’зµ•жџђдє›йЎћећ‹зљ„cookiesе„Іе­ењЁж‚Ёзљ„иЈќзЅ®дёЉгЂ‚ж‚Ёд№џеЏЇд»Ґе€Єй™¤е·Із¶“е„Іе­ењЁж‚ЁиЈќзЅ®дёЉзљ„д»»дЅ•cookiesпјЊдЅ†и«‹иЁдЅЏпјЊе€Єй™¤cookiesеЏЇиѓЅжњѓе°Ћи‡ґж‚Ёз„Ўжі•дЅїз”Ёж€‘еЂ‘з¶Із«™зљ„йѓЁе€†е…§е®№гЂ‚","pc_yprivacy_title":"ж‚Ёзљ„йљ±з§Ѓ","privacy_policy":"<a href=\'%s\' target=\'_blank\'>йљ±з§Ѓж¬Љж”їз­–</a>"}}'
    );
  },
  function (e) {
    e.exports = JSON.parse(
      '{"i18n":{"active":"Activats","always_active":"Totjorn activats","inactive":"Desactivats","nb_agree":"AccГЁpti","nb_changep":"Cambiar mas preferГ©ncias","nb_ok":"D\'acГІrdi","nb_reject":"RegГЁti","nb_text":"Utilizam de cookies e dвЂ™autras tecnologias de seguiment per melhorar vГІstra experiГ©ncia de navegacion sus nГІstre site web, per vos afichar de contenguts personalizats, de publicitats cibladas, per analisar nГІstra audiГ©ncia e per comprendre dвЂ™ont venon nГІstres visitaires.","nb_title":"Utilizam de cookies","pc_fnct_text_1":"Cookies foncionals","pc_fnct_text_2":"Aquestes cookies servisson per vos fornir una experiГ©ncia mai personalizada sus nГІstre site web e per memorizar vГІstras causidas quand navegatz sus nГІstre site web.","pc_fnct_text_3":"Per exemple, podГЁm utilizar de cookies foncionals per memorizar vГІstras preferГ©ncias lingГјisticas o nos remembrar de vГІstre identificant de connexion.","pc_minfo_text_1":"Mai d\'informacions","pc_minfo_text_2":"Per quina question que siГЎ tocant nГІstra politica de cookies e vГІstras causidas, contactat-nos.","pc_minfo_text_3":"Per ne saber mai, consultatz nГІstra <a href=\'%s\' target=\'_blank\'>Politica de confidencialitat</a>.","pc_save":"Enregistrar mas preferГ©ncias","pc_sncssr_text_1":"Cookies formalament necessaris","pc_sncssr_text_2":"Aquestes cookies son essencials per vos fornir los servicis disponibles via nГІstre site web e per vos permetre dвЂ™utilizar dвЂ™unas foncionalitats de nГІstre site web.","pc_sncssr_text_3":"Sens aquestes cookies podГЁm pas vos provesir certans servicis sus nГІstre site web.","pc_title":"Centre de preferГ©ncias dels cookies","pc_trck_text_1":"Cookies de seguiment","pc_trck_text_2":"Aquestes cookies sвЂ™emplegan per collectar dвЂ™informacions per analisar lo trafic de nГІstre site web e coma los visitaires lвЂ™utilizan.","pc_trck_text_3":"Per exemple, aquestes cookies poiriГЎn pistar las causas coma quant de temps passatz sus un site web o las paginas que consultatz, Г§ГІ que nos permet de comprendre coma podГЁm melhorar nГІstre site web per vos.","pc_trck_text_4":"Las informacions collectadas via aqueles cookies de seguiment e de performГ ncia identifican pas individualament cap de visitaire.","pc_trgt_text_1":"Cookies de ciblatge e publicitat","pc_trgt_text_2":"Aquestes cookies servisson per afichar de publicitats que vos interessarГ n probablament basadas sus vГІstras costumas de navegacion.","pc_trgt_text_3":"Aquestes cookies, servits per nГІstres provesidors de contenguts e/o publicitats, pГІdon combinar dвЂ™informacions que collГЁctan de nГІstre site web amb dвЂ™autras informacions quвЂ™an collectadas independentament en relacion amb las activitats de vГІstre navegador a travГЁrs lor malhum de sites web.","pc_trgt_text_4":"Se causissГЁtz de suprimir o desactivar aquestes cookies  publicitaris o de ciblatge, veiretz totjorn de reclamas mas serГ n pas pertinentas per vos.","pc_yprivacy_text_1":"VГІstra vida privada nos impГІrta","pc_yprivacy_text_2":"Los cookies son de plan pichons fichiГЁrs tГЁxt que son gardas dins vГІstre ordenador quand visitatz un site. Utilizam los cookies per mantuna tГІca e per melhorar vГІstra experiГ©ncia en linha sus nГІstre site web (per exemple, per memorizar vГІstre identificant de connexion).","pc_yprivacy_text_3":"PodГЁtz modificar vГІstras preferГ©ncias e regetar certans tipes de cookies de gardar dins vГІstre ordenador en navegant sus nГІstre site web. PodГЁtz tanben suprimir quin cookie que siГЎ ja gardat dins vГІstre ordenador, mas tenГЁtz a l\'esperit que la supression de cookies pГІt empachar dвЂ™utilizar nГІstre site web.","pc_yprivacy_title":"VГІstra confidencialitat"}}'
    );
  },
  function (e, t, i) {
    var n = i(37);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([e.i, '', '']);
  },
  function (e, t) {
    e.exports = function (e) {
      var t = 'undefined' != typeof window && window.location;
      if (!t) throw new Error('fixUrls requires window.location');
      if (!e || 'string' != typeof e) return e;
      var i = t.protocol + '//' + t.host,
        n = i + t.pathname.replace(/\/[^\/]*$/, '/');
      return e.replace(
        /url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi,
        function (e, t) {
          var o,
            a = t
              .trim()
              .replace(/^"(.*)"$/, function (e, t) {
                return t;
              })
              .replace(/^'(.*)'$/, function (e, t) {
                return t;
              });
          return /^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(a)
            ? e
            : ((o =
                0 === a.indexOf('//')
                  ? a
                  : 0 === a.indexOf('/')
                  ? i + a
                  : n + a.replace(/^\.\//, '')),
              'url(' + JSON.stringify(o) + ')');
        }
      );
    };
  },
  function (e, t, i) {
    var n = i(40);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([
      e.i,
      '.termsfeed-com---reset{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;-ms-overflow-style:scrollbar;-webkit-tap-highlight-color:rgba(0,0,0,0);margin:0;font-family:inherit;font-size:1rem;font-weight:400;line-height:1.5;color:#212529;text-align:left;background-color:#fff}.termsfeed-com---reset *,.termsfeed-com---reset *::before,.termsfeed-com---reset *::after{box-sizing:border-box}.termsfeed-com---reset a,.termsfeed-com---reset li,.termsfeed-com---reset p,.termsfeed-com---reset h1,.termsfeed-com---reset h2,.termsfeed-com---reset input,.termsfeed-com---reset button,.termsfeed-com---reset select{border-style:none;box-shadow:none;margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline;outline:none}@-ms-viewport{.termsfeed-com---reset{width:device-width}}.termsfeed-com---reset [tabindex="-1"]:focus{outline:0 !important}.termsfeed-com---reset h1,.termsfeed-com---reset h2,.termsfeed-com---reset h3,.termsfeed-com---reset h4,.termsfeed-com---reset h5,.termsfeed-com---reset h6{margin-top:0;margin-bottom:0;color:#000}.termsfeed-com---reset p{margin-top:0;margin-bottom:1rem}.termsfeed-com---reset div{display:block}.termsfeed-com---reset ol,.termsfeed-com---reset ul,.termsfeed-com---reset dl{margin-top:0;margin-bottom:1rem}.termsfeed-com---reset ol ol,.termsfeed-com---reset ul ul,.termsfeed-com---reset ol ul,.termsfeed-com---reset ul ol{margin-bottom:0}.termsfeed-com---reset b,.termsfeed-com---reset strong{font-weight:bolder}.termsfeed-com---reset small{font-size:80%}.termsfeed-com---reset a{color:#007bff;text-decoration:none;background-color:rgba(0,0,0,0);-webkit-text-decoration-skip:objects}.termsfeed-com---reset a:hover{color:#0056b3;text-decoration:underline}.termsfeed-com---reset a:not([href]):not([tabindex]){color:inherit;text-decoration:none}.termsfeed-com---reset a:not([href]):not([tabindex]):hover,.termsfeed-com---reset a:not([href]):not([tabindex]):focus{color:inherit;text-decoration:none}.termsfeed-com---reset a:not([href]):not([tabindex]):focus{outline:0}.termsfeed-com---reset label{display:inline-block;margin-bottom:.5rem}.termsfeed-com---reset button{border-radius:2px;outline:none;background:#dcdae5;color:#111;cursor:pointer;border:none;transition:all ease .3s}.termsfeed-com---reset button:focus{outline:none}.termsfeed-com---reset select{border-style:none}.termsfeed-com---reset input,.termsfeed-com---reset button,.termsfeed-com---reset select,.termsfeed-com---reset optgroup,.termsfeed-com---reset textarea{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}.termsfeed-com---reset button,.termsfeed-com---reset input{overflow:visible}.termsfeed-com---reset button,.termsfeed-com---reset select{text-transform:none}.termsfeed-com---reset button,.termsfeed-com---reset html [type=button],.termsfeed-com---reset [type=reset],.termsfeed-com---reset [type=submit]{-webkit-appearance:button}.termsfeed-com---reset button::-moz-focus-inner,.termsfeed-com---reset [type=button]::-moz-focus-inner,.termsfeed-com---reset [type=reset]::-moz-focus-inner,.termsfeed-com---reset [type=submit]::-moz-focus-inner{padding:0;border-style:none}.termsfeed-com---reset input[type=radio],.termsfeed-com---reset input[type=checkbox]{box-sizing:border-box;padding:0}.termsfeed-com---reset [hidden]{display:none !important}',
      ''
    ]);
  },
  function (e, t, i) {
    var n = i(42);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([
      e.i,
      '.termsfeed-com---nb{overflow:auto;z-index:9000;font-size:16px}.termsfeed-com---nb .cc-nb-main-container{padding:15px}.termsfeed-com---nb .cc-nb-title{font-size:21px;font-weight:600}.termsfeed-com---nb .cc-nb-text{font-size:13px;line-height: 1.7;margin:0 0 1.25rem 0}.termsfeed-com---nb .cc-nb-okagree,.termsfeed-com---nb .cc-nb-reject,.termsfeed-com---nb .cc-nb-changep{text-transform: uppercase;font-size:14px;margin-right:.9375rem !important;margin-bottom:.25rem !important}@media(max-width: 480px){.termsfeed-com---nb .cc-nb-okagree,.termsfeed-com---nb .cc-nb-reject,.termsfeed-com---nb .cc-nb-changep{display:block;width:100%}}.termsfeed-com---nb-headline{right:0;top:auto;bottom:0;left:0;max-width:100%;position:relative}@media(max-width: 320px),(max-height: 480px){.termsfeed-com---nb-headline{overflow:auto;height:200px;max-width:100%;right:0;top:auto;bottom:0;left:auto;position:fixed}}.termsfeed-com---nb-simple{top:20%;left:20px;position:fixed}@media screen and (min-width: 992px){.termsfeed-com---nb-simple{max-width: 550px}}@media (min-width: 576px) and (max-width: 991px){.termsfeed-com---nb-simple{max-width: 300px}}@media screen and (max-width: 575px){.termsfeed-com---nb-simple{max-width: 100%;left: 0;top: 0}}@media(max-width: 320px),(max-height: 480px){.termsfeed-com---nb-simple{overflow:auto;height:200px;max-width:100%}}.termsfeed-com---nb-interstitial-overlay{position:fixed;top:0;left:0;height:100%;width:100%;background:rgba(0,0,0,.8);z-index:9999999999}.termsfeed-com---nb-interstitial{right:3vw;top:3vh;left:3vw;max-width:100%;position:fixed}@media(max-width: 320px),(max-height: 480px){.termsfeed-com---nb-interstitial{overflow:auto;height:200px;right:0;top:auto;bottom:0;left:auto;position:fixed}}.termsfeed-com---nb-standalone{position:fixed;top:0;left:0;height:100%;width:100%}@media(max-width: 320px),(max-height: 480px){.termsfeed-com---nb-standalone{overflow:auto;height:200px;max-width:100%;right:0;top:auto;bottom:0;left:auto;position:fixed}}.termsfeed-com---pc-overlay{width:100%;height:100%;position:fixed;background:rgba(0,0,0,.5);z-index:999999999999;top:0;left:0;display:none}@media screen and (max-width: 600px){.termsfeed-com---pc-overlay{overflow-y:scroll}}.termsfeed-com---pc-dialog{position:absolute;margin:30px auto;width:750px;max-width:90%;height:auto;left:0;right:0}.termsfeed-com---pc-dialog>div{width:100%}.termsfeed-com---pc-dialog .cc-pc-container{width:100%;display:flex;background:#fff;flex-direction:column}.termsfeed-com---pc-dialog .cc-pc-head{background:#fff;color:#111;display:flex;flex-direction:row;justify-content:space-between}@media screen and (max-width: 600px){.termsfeed-com---pc-dialog .cc-pc-head{flex-direction:column}}.termsfeed-com---pc-dialog .cc-pc-head-title{display:flex;padding-left:15px;flex-direction:column;justify-content:center;align-items:baseline}@media screen and (max-width: 600px){.termsfeed-com---pc-dialog .cc-pc-head-title{align-items:center;padding:15px 0 0 0}}.termsfeed-com---pc-dialog .cc-pc-head-title-text{font-size:16px;line-height:1.5;margin:0}.termsfeed-com---pc-dialog .cc-pc-head-title-headline{font-size:20px;font-weight:600;margin:0}.termsfeed-com---pc-dialog .cc-pc-head-lang{display:flex;align-items:center;padding-right:15px;min-height:80px;justify-content:center}@media screen and (max-width: 600px){.termsfeed-com---pc-dialog .cc-pc-head-lang{padding:15px 0;min-height:20px}}.termsfeed-com---pc-dialog .cc-pc-head-close{display:flex;align-items:center;justify-content:center;margin-left:15px}.termsfeed-com---pc-dialog .cc-cp-body{display:flex;flex-direction:row;align-items:stretch;background:#292929;color:#f5f5f5;border-bottom:none}@media screen and (max-width: 600px){.termsfeed-com---pc-dialog .cc-cp-body{flex-direction:column}}.termsfeed-com---pc-dialog .cc-cp-body-tabs{width:150px;margin:0;padding:0;background:#e6e6e6;min-width:170px}@media screen and (max-width: 600px){.termsfeed-com---pc-dialog .cc-cp-body-tabs{width:100%}}.termsfeed-com---pc-dialog .cc-cp-body-tabs-item{margin:0;padding:0;float:left;display:block;width:100%;color:#666;background:#ededed;border-bottom:1px solid #ccc;border-right:1px solid #ccc;transition:all ease .1s;box-sizing:content-box}@media screen and (max-width: 600px){.termsfeed-com---pc-dialog .cc-cp-body-tabs-item{border-right:0}}.termsfeed-com---pc-dialog .cc-cp-body-tabs-item[active=true]{color:#f5f5f5}.termsfeed-com---pc-dialog .cc-cp-body-tabs-item-link{text-decoration:none;color:#666;display:block;padding:10px 5px 10px 10px;font-weight:500;font-size:14px;line-height:19px;position:relative;cursor:pointer;width:100%;text-align:left;background:none}.termsfeed-com---pc-dialog .cc-cp-body-content{background:#292929;color:#f5f5f5}.termsfeed-com---pc-dialog .cc-cp-body-content-entry{width:100%;display:none;padding:25px;box-sizing:border-box}.termsfeed-com---pc-dialog .cc-cp-body-content-entry[active=true]{display:block}.termsfeed-com---pc-dialog .cc-cp-body-content-entry-title{font-size:24px;font-weight:600}.termsfeed-com---pc-dialog .cc-cp-body-content-entry-text{font-size:16px;line-height:1.5}.termsfeed-com---pc-dialog .cc-cp-foot{background:#f2f2f2;display:flex;flex-direction:row;align-items:center;border-top:1px solid #ccc;justify-content:flex-end;padding-top: 0.9375rem; padding-bottom: 0.9375rem}.termsfeed-com---pc-dialog .cc-cp-foot-byline{padding:20px 10px;font-size:14px;color:#333;display:none !important}.termsfeed-com---pc-dialog .cc-cp-foot-byline a{color:#999}.termsfeed-com---pc-dialog .cc-cp-foot-save{margin-right:10px;opacity:.9;transition:all ease .3s;font-size:14px;font-weight:600;height:auto;text-transform: uppercase;letter-spacing: 0.01em}.termsfeed-com---pc-dialog .cc-cp-foot-save:hover{opacity:1}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox{position:absolute;margin:2px 0 0 16px;cursor:pointer;appearance:none}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox+label{position:relative;padding:4px 0 0 50px;line-height:2em;cursor:pointer;display:inline;font-size:14px}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox+label:before{content:"";position:absolute;display:block;left:0;top:0;width:40px;height:24px;border-radius:16px;background:#fff;border:1px solid #d9d9d9;-webkit-transition:all .3s;transition:all .3s}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox+label:after{content:"";position:absolute;display:block;left:0px;top:0px;width:24px;height:24px;border-radius:16px;background:#fff;border:1px solid #d9d9d9;-webkit-transition:all .3s;transition:all .3s}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox+label:hover:after{box-shadow:0 0 5px rgba(0,0,0,.3)}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox:checked+label:after{margin-left:16px}.termsfeed-com---pc-dialog input[type=checkbox].cc-custom-checkbox:checked+label:before{background:#55d069}',
      ''
    ]);
  },
  function (e, t, i) {
    var n = i(44);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([
      e.i,
      '.termsfeed-com---palette-dark.termsfeed-com---nb{background-color:rgba(59, 59, 59, 0.9);color:#fff}.termsfeed-com---palette-dark .cc-nb-title{color:#fff}.termsfeed-com---palette-dark .cc-nb-text{color:#fff}.termsfeed-com---palette-dark .cc-nb-text a{color:#fff;text-decoration:underline}.termsfeed-com---palette-dark .cc-nb-text a:hover{text-decoration:none}.termsfeed-com---palette-dark .cc-nb-text a:focus{box-shadow:0 0 0 2px #3dd000}.termsfeed-com---palette-dark .cc-nb-okagree{color:#fff;background-color:transparent}.termsfeed-com---palette-dark .cc-nb-reject{color:#fff;background-color:transparent}.termsfeed-com---palette-dark .cc-nb-changep{background-color:transparent;color:#fff}.termsfeed-com---palette-dark .cc-pc-container{background:#212121}.termsfeed-com---palette-dark .cc-pc-head{background:#212121;color:#fff;border-bottom:1px solid rgba(134, 134, 134, 0.5)}.termsfeed-com---palette-dark .cc-pc-head-title-headline{color:#fff}.termsfeed-com---palette-dark .cc-pc-head-title-text{color:#fff}.termsfeed-com---palette-dark .cc-pc-head-lang select{color:#212121;display:none}.termsfeed-com---palette-dark .cc-pc-head-lang select:focus{box-shadow:0 0 0 2px #ff0}.termsfeed-com---palette-dark .cc-pc-head-close{background:none;color:#e6e6e6}.termsfeed-com---palette-dark .cc-cp-body{background:#292929 !important;color:#f5f5f5}.termsfeed-com---palette-dark .cc-cp-body-tabs{color:#666;background:#ededed}.termsfeed-com---palette-dark .cc-cp-body-tabs-item{border-right-color:#ccc;border-bottom-color:rgba(134, 134, 134, 0.1)}.termsfeed-com---palette-dark .cc-cp-body-tabs-item-link{color:#3a3a3a}.termsfeed-com---palette-dark .cc-cp-body-tabs-item-link:hover{color:#666}.termsfeed-com---palette-dark .cc-cp-body-tabs-item[active=true] button{font-weight: 600;color: #000}.termsfeed-com---palette-dark .cc-cp-body-content{background:#292929 !important;color:#f5f5f5}.termsfeed-com---palette-dark .cc-cp-body-content-entry-title{color:#fff}.termsfeed-com---palette-dark .cc-cp-body-content-entry-text{color:#fff}.termsfeed-com---palette-dark .cc-cp-body-content-entry a{color:#fff;text-decoration:underline}.termsfeed-com---palette-dark .cc-cp-body-content-entry a:hover{text-decoration:none}.termsfeed-com---palette-dark .cc-cp-body-content-entry a:focus{box-shadow:0 0 0 2px #ff0}.termsfeed-com---palette-dark .cc-cp-foot{background:#212121;border-top-color:rgba(134, 134, 134, 0.5)}.termsfeed-com---palette-dark .cc-cp-foot-byline{color:#fff}.termsfeed-com---palette-dark .cc-cp-foot-byline a:focus{box-shadow:0 0 0 2px #ff0}.termsfeed-com---palette-dark .cc-cp-foot-save{background:transparent;color:#fff}',
      ''
    ]);
  },
  function (e, t, i) {
    var n = i(46);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([
      e.i,
      '.termsfeed-com---palette-light.termsfeed-com---nb{background-color:#f2f2f2;color:#111}.termsfeed-com---palette-light .cc-nb-title{color:#111}.termsfeed-com---palette-light .cc-nb-text{color:#111}.termsfeed-com---palette-light .cc-nb-text a{color:#111;text-decoration:underline}.termsfeed-com---palette-light .cc-nb-text a:hover{text-decoration:none}.termsfeed-com---palette-light .cc-nb-text a:focus{box-shadow:0 0 0 2px #ff8d00}.termsfeed-com---palette-light .cc-nb-okagree{color:#fff;background-color:green}.termsfeed-com---palette-light .cc-nb-okagree:focus{box-shadow:0 0 0 2px #ff8d00}.termsfeed-com---palette-light .cc-nb-reject{color:#fff;background-color:green}.termsfeed-com---palette-light .cc-nb-reject:focus{box-shadow:0 0 0 2px #ff8d00}.termsfeed-com---palette-light .cc-nb-changep{background-color:#eaeaea;color:#111}.termsfeed-com---palette-light .cc-nb-changep:focus{box-shadow:0 0 0 2px #ff8d00}.termsfeed-com---palette-light .cc-pc-container{background:#fff}.termsfeed-com---palette-light .cc-pc-head{background:#fff;color:#111;border-bottom:1px solid #ccc}.termsfeed-com---palette-light .cc-pc-head-title-headline{color:#111}.termsfeed-com---palette-light .cc-pc-head-title-text{color:#111}.termsfeed-com---palette-light .cc-pc-head-lang select{color:#111}.termsfeed-com---palette-light .cc-pc-head-lang select:focus{box-shadow:0 0 0 2px green}.termsfeed-com---palette-light .cc-pc-head-close{background:none;color:#666}.termsfeed-com---palette-light .cc-pc-head-close:active,.termsfeed-com---palette-light .cc-pc-head-close:focus{border:2px solid green}.termsfeed-com---palette-light .cc-cp-body{background:#fbfbfb !important;color:#111}.termsfeed-com---palette-light .cc-cp-body-tabs{color:#666;background:#e6e6e6}.termsfeed-com---palette-light .cc-cp-body-tabs-item{border-right-color:#ccc;border-bottom-color:#ccc}.termsfeed-com---palette-light .cc-cp-body-tabs-item-link{color:#666}.termsfeed-com---palette-light .cc-cp-body-tabs-item-link:hover{color:#666}.termsfeed-com---palette-light .cc-cp-body-tabs-item-link:focus{box-shadow:0 0 0 2px #fbfbfb}.termsfeed-com---palette-light .cc-cp-body-tabs-item[active=true]{background:#fbfbfb !important}.termsfeed-com---palette-light .cc-cp-body-tabs-item[active=true] button{color:#111}.termsfeed-com---palette-light .cc-cp-body-content{background:#fbfbfb !important;color:#111}.termsfeed-com---palette-light .cc-cp-body-content-entry-title{color:#111}.termsfeed-com---palette-light .cc-cp-body-content-entry-text{color:#111}.termsfeed-com---palette-light .cc-cp-body-content-entry a{color:#111;text-decoration:underline}.termsfeed-com---palette-light .cc-cp-body-content-entry a:hover{text-decoration:none}.termsfeed-com---palette-light .cc-cp-body-content-entry a:focus{box-shadow:0 0 0 2px green}.termsfeed-com---palette-light .cc-cp-foot{background:#f2f2f2;border-top-color:#ccc}.termsfeed-com---palette-light .cc-cp-foot-byline{color:#111}.termsfeed-com---palette-light .cc-cp-foot-byline a:focus{box-shadow:0 0 0 2px green}.termsfeed-com---palette-light .cc-cp-foot-save{background:green;color:#fff}.termsfeed-com---palette-light .cc-cp-foot-save:focus{box-shadow:0 0 0 2px #ff8d00}',
      ''
    ]);
  },
  function (e, t, i) {
    var n = i(48);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([
      e.i,
      '.termsfeed-com---is-hidden{display:none}.termsfeed-com---is-visible{display:block}',
      ''
    ]);
  },
  function (e, t, i) {
    var n = i(50);
    'string' == typeof n && (n = [[e.i, n, '']]);
    var o = { hmr: !0, transform: void 0, insertInto: void 0 };
    i(1)(n, o);
    n.locals && (e.exports = n.locals);
  },
  function (e, t, i) {
    (e.exports = i(0)(!1)).push([
      e.i,
      '.termsfeed-com---nb.termsfeed-com---lang-ar,.termsfeed-com---pc-overlay.termsfeed-com---lang-ar{text-align:right}',
      ''
    ]);
  },
  function (e, t, i) {
    'use strict';
    i.r(t),
      i.d(t, 'run', function () {
        return de;
      }),
      i.d(t, 'cookieConsentObject', function () {
        return o;
      });
    i(36), i(39), i(41), i(43), i(45), i(47), i(49);
    var n,
      o,
      a = (function () {
        function e() {}
        return (
          (e.insertCss = function (e) {
            var t = document.querySelector('head'),
              i = document.createElement('link');
            i.setAttribute('href', e),
              i.setAttribute('rel', 'stylesheet'),
              i.setAttribute('type', 'text/css'),
              t.appendChild(i);
          }),
          (e.appendChild = function (e, t, i) {
            var n, o;
            return (
              void 0 === i && (i = null),
              (n = 'string' == typeof e ? document.querySelector(e) : e),
              (o = 'string' == typeof t ? document.querySelector(t) : t),
              'afterbegin' === i
                ? n.insertAdjacentElement('afterbegin', o)
                : n.insertAdjacentElement('beforeend', o),
              !0
            );
          }),
          (e.setCookie = function (e, t, i, n, o) {
            void 0 === o && (o = 62);
            var a = new Date();
            a.setTime(a.getTime() + 24 * o * 60 * 60 * 1e3);
            var r = '; expires=' + a.toUTCString(),
              s = '; domain=' + i,
              c = '';
            return (
              n && (c = '; Secure'),
              (document.cookie = i
                ? e + '=' + (t || '') + s + r + ';path=/; samesite=strict' + c
                : e + '=' + (t || '') + r + ';path=/; samesite=strict' + c),
              !0
            );
          }),
          (e.getCookie = function (e) {
            for (
              var t = e + '=', i = document.cookie.split(';'), n = 0;
              n < i.length;
              n++
            ) {
              for (var o = i[n]; ' ' === o.charAt(0); )
                o = o.substring(1, o.length);
              if (0 === o.indexOf(t)) return o.substring(t.length, o.length);
            }
            return null;
          }),
          (e.removeCookie = function (e) {
            document.cookie = e + '=; Max-Age=-99999999;';
          }),
          (e.registerEvent = function (e) {
            var t = document.createEvent('Event');
            return t.initEvent(e, !0, !0), t;
          }),
          (e.searchObjectsArray = function (e, t, i) {
            for (var n in e) {
              if (e[n][t] === i) return !0;
            }
            return !1;
          }),
          (e.magicTransform = function (e) {
            return decodeURIComponent(
              atob(e)
                .split('')
                .map(function (e) {
                  return '%' + ('00' + e.charCodeAt(0).toString(16)).slice(-2);
                })
                .join('')
            );
          }),
          (e.isValidUrl = function (e) {
            return new RegExp(
              '^(https?:\\/\\/)((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|((\\d{1,3}\\.){3}\\d{1,3}))(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*(\\?[;&a-z\\d%_.~+=-]*)?(\\#[-a-z\\d_]*)?$',
              'i'
            ).test(e);
          }),
          (e.isBoolean = function (e) {
            return !1 === e || !0 === e;
          }),
          e
        );
      })(),
      r = i(2),
      s = i(3),
      c = i(4),
      l = i(5),
      p = i(6),
      d = i(7),
      u = i(8),
      m = i(9),
      _ = i(10),
      k = i(11),
      v = i(12),
      f = i(13),
      b = i(14),
      h = i(15),
      g = i(16),
      y = i(17),
      x = i(18),
      w = i(19),
      z = i(20),
      j = i(21),
      C = i(22),
      L = i(23),
      A = i(24),
      P = i(25),
      S = i(26),
      E = i(27),
      I = i(28),
      O = i(29),
      T = i(30),
      B = i(31),
      N = i(32),
      U = i(33),
      q = i(34),
      D = i(35),
      M = (function () {
        function e(e) {
          (this.cookieConsent = e),
            (this.userLang = 'en'),
            this.initAvailableLanguages(),
            this.initDefaultTranslations(),
            this.detectUserLanguage();
        }
        return (
          (e.prototype.detectUserLanguage = function () {
            var e = 'en';
            if (
              void 0 !==
              (e =
                void 0 !== navigator.languages
                  ? navigator.languages[0]
                  : navigator.language)
            ) {
              if (e.indexOf('-') > 0) {
                var t = e.split('-');
                e = t[0];
              }
              this.cookieConsent.log(
                '[i18n] Detected owner website language set as: ' + e,
                'info'
              );
            } else e = this.cookieConsent.ownerSiteLanguage;
            var i = e.toLowerCase.toString();
            this.availableTranslations[i]
              ? (this.userLang = i)
              : this.availableTranslations[this.cookieConsent.ownerSiteLanguage]
              ? (this.userLang = this.cookieConsent.ownerSiteLanguage)
              : (this.userLang = 'en');
          }),
          (e.prototype.initDefaultTranslations = function () {
            (this.availableTranslations = {
              en: r,
              en_gb: s,
              de: c,
              fr: l,
              es: p,
              ca_es: d,
              it: u,
              sv: m,
              nl: _,
              pt: k,
              fi: v,
              hu: f,
              hr: b,
              cs: h,
              da: g,
              ro: y,
              sk: x,
              sl: w,
              pl: z,
              sr: j,
              lt: C,
              lv: L,
              ru: A,
              no: P,
              bg: S,
              el: E,
              he: I,
              mk: O,
              cy: T,
              ja: B,
              ar: N,
              tr: U,
              zh_tw: q,
              oc: D
            }),
              this.cookieConsent.log(
                '[i18n] Default translations initialized',
                'info'
              );
          }),
          (e.prototype.initAvailableLanguages = function () {
            (this.availableLanguages = [
              { value: 'en', title: 'English' },
              { value: 'en_gb', title: 'English (UK)' },
              { value: 'de', title: 'German' },
              { value: 'fr', title: 'French' },
              { value: 'es', title: 'Spanish' },
              { value: 'ca_es', title: 'Catalan' },
              { value: 'it', title: 'Italian' },
              { value: 'sv', title: 'Swedish' },
              { value: 'nl', title: 'Dutch' },
              { value: 'pt', title: 'Portuguese' },
              { value: 'fi', title: 'Finnish' },
              { value: 'hu', title: 'Hungarian' },
              { value: 'hr', title: 'Croatian' },
              { value: 'cs', title: 'Czech' },
              { value: 'da', title: 'Danish' },
              { value: 'ro', title: 'Romanian' },
              { value: 'sk', title: 'Slovak' },
              { value: 'sl', title: 'Slovenian' },
              { value: 'pl', title: 'Polish' },
              { value: 'sr', title: 'Serbian' },
              { value: 'lt', title: 'Lithuanian' },
              { value: 'lv', title: 'Latvian' },
              { value: 'ru', title: 'Russian' },
              { value: 'no', title: 'Norwegian' },
              { value: 'bg', title: 'Bulgarian' },
              { value: 'el', title: 'Greek' },
              { value: 'he', title: 'Hebrew' },
              { value: 'mk', title: 'Macedonian' },
              { value: 'cy', title: 'Welsh' },
              { value: 'ja', title: 'Japanese' },
              { value: 'ar', title: 'Arabic' },
              { value: 'tr', title: 'Turkish' },
              { value: 'zh_tw', title: 'Traditional Chinese (zh-TW)' },
              { value: 'oc', title: 'Occitan' }
            ]),
              this.cookieConsent.log(
                '[i18n] Default languages initialized',
                'info'
              );
          }),
          (e.prototype.$t = function (e, t, i) {
            void 0 === i && (i = null);
            var n = this.availableTranslations[this.userLang][e][t];
            return (
              'string' == typeof i
                ? (n = n.replace('%s', i))
                : Array.isArray(i) &&
                  i.map(function (e, t) {
                    var o = i[t];
                    n = n.replace('%s', o);
                  }),
              n || ''
            );
          }),
          e
        );
      })(),
      J =
        ((n = function (e, t) {
          return (n =
            Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array &&
              function (e, t) {
                e.__proto__ = t;
              }) ||
            function (e, t) {
              for (var i in t) t.hasOwnProperty(i) && (e[i] = t[i]);
            })(e, t);
        }),
        function (e, t) {
          function i() {
            this.constructor = e;
          }
          n(e, t),
            (e.prototype =
              null === t
                ? Object.create(t)
                : ((i.prototype = t.prototype), new i()));
        }),
      W = function (e, t) {
        var i = 'function' == typeof Symbol && e[Symbol.iterator];
        if (!i) return e;
        var n,
          o,
          a = i.call(e),
          r = [];
        try {
          for (; (void 0 === t || t-- > 0) && !(n = a.next()).done; )
            r.push(n.value);
        } catch (e) {
          o = { error: e };
        } finally {
          try {
            n && !n.done && (i = a.return) && i.call(a);
          } finally {
            if (o) throw o.error;
          }
        }
        return r;
      },
      F = function () {
        for (var e = [], t = 0; t < arguments.length; t++)
          e = e.concat(W(arguments[t]));
        return e;
      },
      R = function (e) {
        var t = 'function' == typeof Symbol && Symbol.iterator,
          i = t && e[t],
          n = 0;
        if (i) return i.call(e);
        if (e && 'number' == typeof e.length)
          return {
            next: function () {
              return (
                e && n >= e.length && (e = void 0),
                { value: e && e[n++], done: !e }
              );
            }
          };
        throw new TypeError(
          t ? 'Object is not iterable.' : 'Symbol.iterator is not defined.'
        );
      },
      V = (function () {
        function e(e) {
          (this.acceptedLevels = {}),
            (this.userAccepted = !1),
            (this.consentLevelCookieName = 'cookie_consent_level'),
            (this.consentAcceptedCookieName = 'cookie_consent_user_accepted'),
            (this.cookieConsent = e),
            this.cookieConsent.log('CookieConsent initialized', 'info'),
            this.checkIfUserAccepted(),
            this.getUserLevels();
        }
        return (
          (e.prototype.checkIfUserAccepted = function () {
            'true' === a.getCookie(this.consentAcceptedCookieName) &&
              (this.userAccepted = !0);
          }),
          (e.prototype.markUserAccepted = function () {
            (this.userAccepted = !0),
              !1 === this.cookieConsent.isDemo &&
                a.setCookie(
                  this.consentAcceptedCookieName,
                  'true',
                  this.cookieConsent.ownerDomain,
                  this.cookieConsent.cookieSecure,
                  this.cookieConsent.cookieDuration
                );
          }),
          (e.prototype.getUserLevels = function () {
            var e = a.getCookie(this.consentLevelCookieName),
              t = {};
            try {
              t = JSON.parse(decodeURIComponent(e));
            } catch (e) {
              t = null;
            }
            if (null === t)
              document.dispatchEvent(this.cookieConsent.events.cc_freshUser),
                (this.acceptedLevels['strictly-necessary'] = !0),
                'implied' === this.cookieConsent.ownerConsentType
                  ? ((this.acceptedLevels.functionality = !0),
                    (this.acceptedLevels.tracking = !0),
                    (this.acceptedLevels.targeting = !0))
                  : 'express' === this.cookieConsent.ownerConsentType &&
                    ((this.acceptedLevels.functionality = !1),
                    (this.acceptedLevels.tracking = !1),
                    (this.acceptedLevels.targeting = !1));
            else
              for (var i in this.cookieConsent.cookieLevels.cookieLevels) {
                var n = this.cookieConsent.cookieLevels.cookieLevels[i].id;
                !0 === t[n]
                  ? (this.acceptedLevels[n] = !0)
                  : (this.acceptedLevels[n] = !1),
                  this.saveCookie();
              }
            this.cookieConsent.log(
              'Proposed accepted levels based on consent type are:',
              'info'
            ),
              this.cookieConsent.log(this.acceptedLevels, 'info', 'table');
          }),
          (e.prototype.acceptAllCookieLevels = function () {
            for (var e in this.cookieConsent.cookieLevels.cookieLevels) {
              var t = this.cookieConsent.cookieLevels.cookieLevels[e].id;
              this.acceptLevel(t);
            }
            document.dispatchEvent(
              this.cookieConsent.events.cc_scriptsAllLoaded
            );
          }),
          (e.prototype.rejectAllCookieLevels = function () {
            for (var e in this.cookieConsent.cookieLevels.cookieLevels) {
              var t = this.cookieConsent.cookieLevels.cookieLevels[e].id;
              'strictly-necessary' != t
                ? this.rejectLevel(t)
                : 'strictly-necessary' == t && this.acceptLevel(t);
            }
          }),
          (e.prototype.loadAcceptedCookies = function () {
            for (var e in this.cookieConsent.cookieLevels.cookieLevels) {
              var t = this.cookieConsent.cookieLevels.cookieLevels[e].id;
              !1 !== this.acceptedLevels[t] &&
                this.cookieConsent.javascriptItems.enableScriptsByLevel(t);
            }
          }),
          (e.prototype.acceptLevel = function (e) {
            return (
              this.cookieConsent.log('Accepted cookie level: ' + e, 'info'),
              (this.acceptedLevels[e] = !0),
              this.saveCookie()
            );
          }),
          (e.prototype.rejectLevel = function (e) {
            return (
              this.cookieConsent.log('Rejected cookie level: ' + e, 'info'),
              (this.acceptedLevels[e] = !1),
              this.saveCookie()
            );
          }),
          (e.prototype.saveCookie = function () {
            var e = encodeURIComponent(JSON.stringify(this.acceptedLevels));
            return (
              a.setCookie(
                this.consentLevelCookieName,
                e,
                this.cookieConsent.ownerDomain,
                this.cookieConsent.cookieSecure,
                this.cookieConsent.cookieDuration
              ),
              this.cookieConsent.log(
                'Saved cookie with user consent level',
                'info'
              ),
              !0
            );
          }),
          e
        );
      })(),
      K = function () {
        (this.cc_noticeBannerShown = a.registerEvent('cc_noticeBannerShown')),
          (this.cc_noticeBannerOkOrAgreePressed = a.registerEvent(
            'cc_noticeBannerOkOrAgreePressed'
          )),
          (this.cc_noticeBannerRejectPressed = a.registerEvent(
            'cc_noticeBannerRejectPressed'
          )),
          (this.cc_noticeBannerChangePreferencesPressed = a.registerEvent(
            'cc_noticeBannerChangePreferencesPressed'
          )),
          (this.cc_preferencesCenterClosePressed = a.registerEvent(
            'cc_preferencesCenterClosePressed'
          )),
          (this.cc_preferencesCenterSavePressed = a.registerEvent(
            'cc_preferencesCenterSavePressed'
          )),
          (this.cc_userLanguageChanged = a.registerEvent(
            'cc_userLanguageChanged'
          )),
          (this.cc_freshUser = a.registerEvent('cc_freshUser')),
          (this.cc_userChangedConsent = a.registerEvent(
            'cc_userChangedConsent'
          )),
          (this.cc_scriptsAllLoaded = a.registerEvent('cc_scriptsAllLoaded')),
          (this.cc_scriptsSpecificLoaded = a.registerEvent(
            'cc_scriptsSpecificLoaded'
          ));
      },
      $ = (function () {
        function e(e) {
          (this.scripts = {}),
            (this.cookieConsent = e),
            this.cookieConsent.log('Cookie Consent initialized', 'info'),
            this.readScripts();
        }
        return (
          (e.prototype.readScripts = function () {
            var e = document.querySelectorAll('script[type="text/plain"]');
            for (var t in e) {
              var i = e[t];
              'object' == typeof i && this._noticeScriptIfValid(i);
            }
          }),
          (e.prototype._noticeScriptIfValid = function (e) {
            var t = e.getAttribute('data-cookie-consent');
            !0 ===
            a.searchObjectsArray(
              this.cookieConsent.cookieLevels.cookieLevels,
              'id',
              t
            )
              ? (this.cookieConsent.log(
                  'JavaScript script with valid data-cookie-consent tag found, but not loaded yet:',
                  'info'
                ),
                this.cookieConsent.log(e, 'info'),
                void 0 === this.scripts[t] && (this.scripts[t] = []),
                this.scripts[t].push(e))
              : this.cookieConsent.log(
                  'Invalid cookie-consent tag level for JavaScript script: ' +
                    t,
                  'warning'
                );
          }),
          (e.prototype.enableScriptsByLevel = function (e) {
            var t = this;
            new Promise(function (i, n) {
              var o = !1;
              if (!t.scripts[e]) return i(o);
              o = t.scripts[e].length > 0;
              var r = function (i) {
                try {
                  var n = t.scripts[e][i],
                    o = F(n.attributes),
                    r = document.createElement('script');
                  r.setAttribute('type', 'text/javascript'),
                    r.setAttribute(
                      'initial-data-cookie-consent',
                      n.getAttribute('data-cookie-consent')
                    ),
                    null !== n.getAttribute('src') &&
                      r.setAttribute('src', n.getAttribute('src')),
                    o.reduce(function (e, t) {
                      'data-cookie-consent' !== t.name &&
                        'type' !== t.name &&
                        r.setAttribute(t.name, t.value);
                    }, {}),
                    (r.text = n.innerHTML),
                    a.appendChild('head', r),
                    n.parentNode.removeChild(n);
                } catch (e) {
                  t.cookieConsent.log(
                    'Error while trying to enable a JavaScript script: ' +
                      e.message.toString(),
                    'log'
                  );
                }
                delete t.scripts[e][i];
              };
              for (var s in t.scripts[e]) r(s);
              i(o);
            }).then(function (i) {
              if (i) {
                var n = t.cookieConsent.events.cc_scriptsSpecificLoaded;
                (n.level = e), document.dispatchEvent(n);
              }
            });
          }),
          e
        );
      })(),
      H = (function () {
        function e(e) {
          (this.cookieConsent = e),
            this.cc_noticeBannerShown(),
            this.cc_noticeBannerOkOrAgreePressed(),
            this.cc_preferencesCenterClosePressed(),
            this.cc_noticeBannerRejectPressed(),
            this.cc_noticeBannerChangePreferencesPressed(),
            this.cc_userLanguageChanged(),
            this.cc_preferencesCenterSavePressed(),
            this.cc_freshUser(),
            this.cc_userChangedConsent(),
            this.cc_scriptsAllLoaded(),
            this.cc_scriptsSpecificLoaded();
        }
        return (
          (e.prototype.cc_noticeBannerShown = function () {
            var e = this;
            window.addEventListener('cc_noticeBannerShown', function () {
              e.cookieConsent.log('cc_noticeBannerShown triggered', 'event'),
                e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                  'cc_noticeBannerShown'
                );
            });
          }),
          (e.prototype.cc_noticeBannerOkOrAgreePressed = function () {
            var e = this;
            document.addEventListener(
              'cc_noticeBannerOkOrAgreePressed',
              function () {
                (this.userConsentTokenClass = new le(e.cookieConsent)),
                  e.cookieConsent.log(
                    'cc_noticeBannerOkOrAgreePressed triggered',
                    'event'
                  ),
                  e.cookieConsent.userConsent.acceptAllCookieLevels(),
                  e.cookieConsent.userConsent.markUserAccepted(),
                  e.cookieConsent.pageRefreshConfirmationButtons
                    ? window.location.reload()
                    : e.cookieConsent.userConsent.loadAcceptedCookies(),
                  e.cookieConsent.noticeBannerContainer.hideNoticeBanner(),
                  e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                    'cc_noticeBannerOkOrAgreePressed'
                  );
              }
            );
          }),
          (e.prototype.cc_noticeBannerRejectPressed = function () {
            var e = this;
            window.addEventListener(
              'cc_noticeBannerRejectPressed',
              function () {
                (this.userTokenClass = new le(e.cookieConsent)),
                  e.cookieConsent.log(
                    'cc_noticeBannerRejectPressed triggered',
                    'event'
                  ),
                  e.cookieConsent.userConsent.rejectAllCookieLevels(),
                  e.cookieConsent.userConsent.markUserAccepted(),
                  e.cookieConsent.noticeBannerContainer.hideNoticeBanner(),
                  e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                    'cc_noticeBannerRejectPressed'
                  ),
                  e.cookieConsent.pageRefreshConfirmationButtons &&
                    window.location.reload();
              }
            );
          }),
          (e.prototype.cc_noticeBannerChangePreferencesPressed = function () {
            var e = this;
            window.addEventListener(
              'cc_noticeBannerChangePreferencesPressed',
              function () {
                e.cookieConsent.log(
                  'cc_noticeBannerChangePreferencesPressed triggered',
                  'event'
                ),
                  e.cookieConsent.preferencesCenterContainer.showPreferencesCenter(),
                  e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                    'cc_noticeBannerChangePreferencesPressed'
                  );
              }
            );
          }),
          (e.prototype.cc_userLanguageChanged = function () {
            var e = this;
            window.addEventListener('cc_userLanguageChanged', function () {
              e.cookieConsent.log('cc_userLanguageChanged triggered', 'event'),
                e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                  'cc_userLanguageChanged'
                );
            });
          }),
          (e.prototype.cc_preferencesCenterClosePressed = function () {
            var e = this;
            document.addEventListener(
              'cc_preferencesCenterClosePressed',
              function () {
                e.cookieConsent.log(
                  'cc_preferencesCenterClosePressed triggered',
                  'event'
                ),
                  e.cookieConsent.preferencesCenterContainer.hidePreferencesCenter(),
                  e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                    'cc_preferencesCenterClosePressed'
                  );
              }
            );
          }),
          (e.prototype.cc_preferencesCenterSavePressed = function () {
            var e = this;
            window.addEventListener(
              'cc_preferencesCenterSavePressed',
              function () {
                (this.userConsentTokenClass = new le(e.cookieConsent)),
                  e.cookieConsent.log(
                    'cc_preferencesCenterSavePressed triggered',
                    'event'
                  ),
                  e.cookieConsent.userConsent.markUserAccepted(),
                  e.cookieConsent.userConsent.saveCookie(),
                  e.cookieConsent.userConsent.loadAcceptedCookies(),
                  e.cookieConsent.preferencesCenterContainer.hidePreferencesCenter(),
                  e.cookieConsent.noticeBannerContainer.hideNoticeBanner(),
                  e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                    'cc_preferencesCenterSavePressed'
                  ),
                  e.cookieConsent.pageRefreshConfirmationButtons &&
                    window.location.reload();
              }
            );
          }),
          (e.prototype.cc_freshUser = function () {
            var e = this;
            window.addEventListener('cc_freshUser', function () {
              e.cookieConsent.log('cc_freshUser triggered', 'event'),
                e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                  'cc_freshUser'
                );
            });
          }),
          (e.prototype.cc_userChangedConsent = function () {
            var e = this;
            window.addEventListener('cc_userChangedConsent', function () {
              e.cookieConsent.log('cc_userChangedConsent triggered', 'event'),
                e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                  'cc_userChangedConsent'
                );
            });
          }),
          (e.prototype.cc_scriptsAllLoaded = function () {
            var e = this;
            window.addEventListener('cc_scriptsAllLoaded', function () {
              e.cookieConsent.log('cc_scriptsAllLoaded triggered', 'event'),
                e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                  'cc_scriptsAllLoaded'
                );
            });
          }),
          (e.prototype.cc_scriptsSpecificLoaded = function () {
            var e = this;
            window.addEventListener('cc_scriptsSpecificLoaded', function (t) {
              e.cookieConsent.log(
                'cc_scriptsSpecificLoaded triggered',
                'event'
              ),
                e.cookieConsent.customerCallbacks.callCustomerCallbackByEvent(
                  'cc_scriptsSpecificLoaded',
                  t.level
                );
            });
          }),
          e
        );
      })(),
      G = (function () {
        function e(e, t) {
          (this.cookieConsent = e),
            this.cookieConsent.log('CustomerCallbacks initialized', 'info'),
            this.cookieConsent.log(t, 'info'),
            (this.customerCallbacksObject = t);
        }
        return (
          (e.prototype.callCustomerCallbackByEvent = function (e, t) {
            void 0 === t && (t = null);
            try {
              var i = {
                cc_noticeBannerShown: 'notice_banner_loaded',
                cc_noticeBannerOkOrAgreePressed: 'i_agree_button_clicked',
                cc_noticeBannerRejectPressed: 'i_decline_button_clicked',
                cc_noticeBannerChangePreferencesPressed:
                  'change_my_preferences_button_clicked',
                cc_scriptsAllLoaded: 'scripts_all_loaded',
                cc_scriptsSpecificLoaded: 'scripts_specific_loaded'
              }[e];
              this.cookieConsent.log('Calling Customer Callback: ' + i, 'info'),
                this.customerCallbacksObject[i](t);
            } catch (e) {}
          }),
          e
        );
      })(),
      Z = (function () {
        function e(e) {
          (this.cookieConsent = e), this.initPreferenceItems();
        }
        return (
          (e.prototype.languageChanged = function () {
            this.initPreferenceItems();
          }),
          (e.prototype.initPreferenceItems = function () {
            var e, t;
            (this.preferenceItems = [
              {
                title: this.cookieConsent.i18n.$t('i18n', 'pc_yprivacy_title'),
                title_container: 'title_your_privacy',
                content_container: 'content_your_privacy',
                content:
                  "<p class='cc-cp-body-content-entry-title'>" +
                  this.cookieConsent.i18n.$t('i18n', 'pc_yprivacy_text_1') +
                  "</p><p class='cc-cp-body-content-entry-text'>" +
                  this.cookieConsent.i18n.$t('i18n', 'pc_yprivacy_text_2') +
                  "</p><p class='cc-cp-body-content-entry-text'>" +
                  this.cookieConsent.i18n.$t('i18n', 'pc_yprivacy_text_3') +
                  '</p>'
              }
            ]),
              (this.cookieLevels = [
                {
                  id: 'strictly-necessary',
                  title: this.cookieConsent.i18n.$t('i18n', 'pc_sncssr_text_1'),
                  content:
                    "<p class='cc-cp-body-content-entry-title'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_sncssr_text_1') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_sncssr_text_2') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_sncssr_text_3') +
                    '</p>'
                },
                {
                  id: 'functionality',
                  title: this.cookieConsent.i18n.$t('i18n', 'pc_fnct_text_1'),
                  content:
                    "<p class='cc-cp-body-content-entry-title'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_fnct_text_1') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_fnct_text_2') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_fnct_text_3') +
                    '</p>'
                },
                {
                  id: 'tracking',
                  title: this.cookieConsent.i18n.$t('i18n', 'pc_trck_text_1'),
                  content:
                    "<p class='cc-cp-body-content-entry-title'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trck_text_1') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trck_text_2') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trck_text_3') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trck_text_4') +
                    '</p>'
                },
                {
                  id: 'targeting',
                  title: this.cookieConsent.i18n.$t('i18n', 'pc_trgt_text_1'),
                  content:
                    "<p class='cc-cp-body-content-entry-title'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trgt_text_1') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trgt_text_2') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trgt_text_3') +
                    "</p><p class='cc-cp-body-content-entry-text'>" +
                    this.cookieConsent.i18n.$t('i18n', 'pc_trgt_text_4') +
                    '</p>'
                }
              ]);
            try {
              for (
                var i = R(this.cookieLevels), n = i.next();
                !n.done;
                n = i.next()
              ) {
                var o = n.value;
                this.preferenceItems.push({
                  id: o.id,
                  title: o.title,
                  title_container: 'title_' + o.id,
                  content_container: 'content_' + o.id,
                  content: o.content
                });
              }
            } catch (t) {
              e = { error: t };
            } finally {
              try {
                n && !n.done && (t = i.return) && t.call(i);
              } finally {
                if (e) throw e.error;
              }
            }
            this.preferenceItems.push({
              title: this.cookieConsent.i18n.$t('i18n', 'pc_minfo_text_1'),
              title_container: 'title_more_information',
              content_container: 'content_more_information',
              content:
                "<p class='cc-cp-body-content-entry-title'>" +
                this.cookieConsent.i18n.$t('i18n', 'pc_minfo_text_1') +
                "</p><p class='cc-cp-body-content-entry-text'>" +
                this.cookieConsent.i18n.$t('i18n', 'pc_minfo_text_2') +
                '</p>'
            }),
              null !== this.cookieConsent.ownerWebsitePrivacyPolicyUrl &&
                a.isValidUrl(this.cookieConsent.ownerWebsitePrivacyPolicyUrl) &&
                (this.preferenceItems[this.preferenceItems.length - 1].content =
                  this.preferenceItems[this.preferenceItems.length - 1]
                    .content +
                  "<p class='cc-cp-body-content-entry-text'>" +
                  this.cookieConsent.i18n.$t(
                    'i18n',
                    'pc_minfo_text_3',
                    this.cookieConsent.ownerWebsitePrivacyPolicyUrl
                  ) +
                  '</p>');
          }),
          e
        );
      })(),
      Y = (function () {
        function e(e) {
          (this.preferencesCenterOverlay = null), (this.cookieConsent = e);
        }
        return (
          (e.prototype.listenToUserButtonToOpenPreferences = function (e) {
            var t = this,
              i = document.querySelectorAll(e);
            t.cookieConsent.log('userButton detected:', 'info'),
              t.cookieConsent.log(i, 'info', 'table'),
              i &&
                i.forEach(function (e) {
                  e.addEventListener('click', function () {
                    document.dispatchEvent(
                      t.cookieConsent.events
                        .cc_noticeBannerChangePreferencesPressed
                    ),
                      t.showPreferencesCenter();
                  });
                });
          }),
          (e.prototype.showPreferencesCenter = function () {
            var e,
              t = this;
            null === this.preferencesCenterOverlay &&
              ((this.preferencesCenterOverlay =
                this.createPreferencesCenterOverlayAndDialog()),
              a.appendChild('body', this.preferencesCenterOverlay)),
              this.preferencesCenterOverlay.classList.add(
                'termsfeed-com---is-visible'
              ),
              t.cookieConsent.log('Preferences Center shown', 'info'),
              this.preferencesCenterOverlay.setAttribute('role', 'dialog'),
              this.preferencesCenterOverlay.setAttribute(
                'aria-labelledby',
                'cc-pc-head-title-headline'
              ),
              this.preferencesCenterOverlay.setAttribute('tabindex', '-1'),
              this.preferencesCenterOverlay.focus();
            var i = document.querySelector(
                '#termsfeed-com---preferences-center'
              ),
              n = i.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
              )[0],
              o = i.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
              ),
              r = o[o.length - 1];
            t.cookieConsent.log(
              'preferencesCenterOverlayModal_firstFocusableElement: ' + n,
              'info'
            ),
              t.cookieConsent.log(
                'preferencesCenterOverlayModal_focusableContent: ' + o,
                'info'
              ),
              t.cookieConsent.log(
                'preferencesCenterOverlayModal_lastFocusableElement: ' + r,
                'info'
              ),
              document.addEventListener('keydown', function (e) {
                var i, o;
                ('Tab' === e.key || 9 === e.keyCode) &&
                  (e.shiftKey
                    ? document.activeElement === n &&
                      (t.cookieConsent.log(
                        'preferencesCenterOverlayModal_lastFocusableElement before focus: ' +
                          r,
                        'info'
                      ),
                      null === (i = r) || void 0 === i || i.focus(),
                      e.preventDefault())
                    : document.activeElement === r &&
                      (t.cookieConsent.log(
                        'preferencesCenterOverlayModal_firstFocusableElement before focus: ' +
                          n,
                        'info'
                      ),
                      null === (o = n) || void 0 === o || o.focus(),
                      e.preventDefault()));
              }),
              t.cookieConsent.log(
                'preferencesCenterOverlayModal_firstFocusableElement before focus: ' +
                  n,
                'info'
              ),
              null === (e = n) || void 0 === e || e.focus(),
              this.preferencesCenterOverlay.classList.add(
                'termsfeed-com---lang-' + t.cookieConsent.i18n.userLang
              );
          }),
          (e.prototype.hidePreferencesCenter = function () {
            this.preferencesCenterOverlay.classList.remove(
              'termsfeed-com---is-visible'
            ),
              this.cookieConsent.log('Preferences Center hidden', 'info');
          }),
          (e.prototype.refreshPreferencesCenter = function () {
            if (null !== this.preferencesCenterOverlay)
              return (
                this.preferencesCenterOverlay.parentNode.removeChild(
                  this.preferencesCenterOverlay
                ),
                (this.preferencesCenterOverlay = null),
                this.showPreferencesCenter()
              );
          }),
          (e.prototype.createPreferencesCenterOverlayAndDialog = function () {
            var e = this,
              t = document.createElement('div');
            t.classList.add('termsfeed-com---pc-overlay'),
              t.classList.add(e.cookieConsent.colorPalette.getClass()),
              t.classList.add('termsfeed-com---reset'),
              (t.id = 'termsfeed-com---preferences-center'),
              t.setAttribute('id', 'termsfeed-com---preferences-center');
            var i = document.createElement('div');
            i.classList.add('termsfeed-com---pc-dialog');
            var n = document.createElement('div');
            n.classList.add('cc-pc-container');
            var o = document.createElement('div');
            o.classList.add('cc-pc-head');
            var r = document.createElement('div');
            if (
              (r.classList.add('cc-pc-head-title'),
              e.cookieConsent.ownerWebsiteName.length > 2)
            ) {
              var s = document.createElement('p');
              s.classList.add('cc-pc-head-title-text'),
                (s.innerText = e.cookieConsent.ownerWebsiteName),
                a.appendChild(r, s);
            }
            var c = document.createElement('p');
            c.classList.add('cc-pc-head-title-headline'),
              c.setAttribute('id', 'cc-pc-head-title-headline'),
              (c.innerHTML = e.cookieConsent.i18n.$t('i18n', 'pc_title')),
              a.appendChild(r, c);
            var l = document.createElement('div');
            l.classList.add('cc-pc-head-lang');
            var p = this.obtainLanguageSelector();
            a.appendChild(l, p);
            var d = document.createElement('button');
            d.classList.add('cc-pc-head-close'),
              (d.innerHTML = '&#x2715;'),
              d.addEventListener('click', function () {
                document.dispatchEvent(
                  e.cookieConsent.events.cc_preferencesCenterClosePressed
                );
              }),
              a.appendChild(o, r),
              a.appendChild(o, l),
              !1 === e.cookieConsent.ownerPreferencesCenterCloseButtonHide &&
                a.appendChild(l, d, 'afterend');
            var u = document.createElement('div');
            u.classList.add('cc-cp-body');
            var m = this.getMenuContainer(),
              _ = this.getContentContainer();
            a.appendChild(u, m), a.appendChild(u, _);
            var k = this.getFooterContainer();
            return (
              a.appendChild(n, o),
              a.appendChild(n, u),
              a.appendChild(n, k),
              a.appendChild(i, n),
              a.appendChild(t, i),
              t
            );
          }),
          (e.prototype.obtainLanguageSelector = function () {
            var e = this,
              t = document.createElement('select');
            return (
              t.classList.add('cc-pc-head-lang-select'),
              [].forEach.call(
                e.cookieConsent.i18n.availableLanguages,
                function (i) {
                  var n = document.createElement('option');
                  (n.text = i.title),
                    (n.value = i.value),
                    e.cookieConsent.i18n.userLang === n.value &&
                      n.setAttribute('selected', 'selected'),
                    t.add(n);
                }
              ),
              t.addEventListener('change', function () {
                (e.cookieConsent.i18n.userLang = t.value),
                  e.cookieConsent.cookieLevels.languageChanged(),
                  e.refreshPreferencesCenter(),
                  document.dispatchEvent(
                    e.cookieConsent.events.cc_userLanguageChanged
                  );
              }),
              t
            );
          }),
          (e.prototype.getContentContainer = function () {
            var e = this,
              t = document.createElement('div');
            t.classList.add('cc-cp-body-content');
            var i = 0;
            return (
              e.cookieConsent.cookieLevels.preferenceItems.forEach(function (
                n
              ) {
                var o = document.createElement('div');
                if (
                  (o.classList.add('cc-cp-body-content-entry'),
                  o.setAttribute('id', n.content_container),
                  o.setAttribute('role', 'tabpanel'),
                  o.setAttribute('aria-labelledby', n.title_container),
                  o.setAttribute('hidden', ''),
                  o.setAttribute('tabindex', '0'),
                  o.setAttribute('content_layout', n.content_container),
                  o.setAttribute('active', 'false'),
                  (o.innerHTML = n.content),
                  0 === i &&
                    (o.setAttribute('active', 'true'),
                    o.removeAttribute('hidden')),
                  i++,
                  n.id)
                ) {
                  var r = e._getLevelCheckbox(n);
                  a.appendChild(o, r);
                }
                a.appendChild(t, o);
              }),
              t
            );
          }),
          (e.prototype.getMenuContainer = function () {
            var e = this,
              t = document.createElement('ul');
            t.classList.add('cc-cp-body-tabs'),
              t.setAttribute('role', 'tablist'),
              t.setAttribute('aria-label', 'Menu');
            var i = 0;
            return (
              e.cookieConsent.cookieLevels.preferenceItems.forEach(function (
                n
              ) {
                var o = document.createElement('li');
                o.classList.add('cc-cp-body-tabs-item');
                var r = document.createElement('button');
                r.classList.add('cc-cp-body-tabs-item-link'),
                  r.setAttribute('id', n.title_container),
                  r.setAttribute('role', 'tab'),
                  r.setAttribute('aria-selected', 'false'),
                  r.setAttribute('aria-controls', n.content_container),
                  r.setAttribute('tabindex', '-1'),
                  r.setAttribute('t', n.content_container),
                  (r.innerHTML = n.title),
                  0 === i &&
                    (o.setAttribute('active', 'true'),
                    r.setAttribute('aria-selected', 'true'),
                    r.setAttribute('tabindex', '0')),
                  i++,
                  r.addEventListener('click', function (t) {
                    t.preventDefault(),
                      e.cookieConsent.log(
                        'Preferences Center tab item clicked: ' + n.title,
                        'info'
                      );
                    var i = document.querySelectorAll('li[active="true"]');
                    [].forEach.call(i, function (e) {
                      e.setAttribute('active', 'false'),
                        e.firstElementChild.setAttribute(
                          'aria-selected',
                          'false'
                        ),
                        e.firstElementChild.setAttribute('tabindex', '-1');
                    }),
                      o.setAttribute('active', 'true'),
                      o.firstElementChild.setAttribute('aria-selected', 'true'),
                      o.firstElementChild.setAttribute('tabindex', '0');
                    try {
                      var a = document.querySelectorAll('div[content_layout]');
                      [].forEach.call(a, function (e) {
                        e.setAttribute('active', 'false'),
                          e.setAttribute('hidden', '');
                      });
                      var r = document.querySelector(
                        'div[content_layout="' + n.content_container + '"]'
                      );
                      r.setAttribute('active', 'true'),
                        r.removeAttribute('hidden');
                    } catch (t) {}
                  });
                var s = 0,
                  c = document.getElementsByClassName(
                    'cc-cp-body-tabs-item-link'
                  );
                t.addEventListener('keydown', function (e) {
                  ('ArrowDown' !== e.key &&
                    'ArrowUp' !== e.key &&
                    'ArrowLeft' !== e.key &&
                    'ArrowRight' !== e.key) ||
                    (c[s].setAttribute('tabindex', '-1'),
                    'ArrowDown' === e.key || 'ArrowRight' === e.key
                      ? ++s >= c.length && (s = 0)
                      : ('ArrowUp' !== e.key && 'ArrowLeft' !== e.key) ||
                        (--s < 0 && (s = c.length - 1)),
                    c[s].setAttribute('tabindex', '0'),
                    c[s].focus());
                }),
                  a.appendChild(o, r),
                  a.appendChild(t, o);
              }),
              t
            );
          }),
          (e.prototype.getFooterContainer = function () {
            var e = this,
              t = document.createElement('div');
            t.classList.add('cc-cp-foot');
            var i = document.createElement('div');
            i.classList.add('cc-cp-foot-byline'),
              (i.innerHTML = a.magicTransform(
                'Q29va2llIENvbnNlbnQgYnkgPGEgaHJlZj0iaHR0cHM6Ly93d3cudGVybXNmZWVkLmNvbS9jb29raWUtY29uc2VudC8iIHRhcmdldD0iX2JsYW5rIj5UZXJtc0ZlZWQ8L2E+'
              ));
            var n = document.createElement('div');
            n.classList.add('cc-cp-foot-button');
            var o = document.createElement('button');
            return (
              o.classList.add('cc-cp-foot-save'),
              (o.innerHTML = e.cookieConsent.i18n.$t('i18n', 'pc_save')),
              o.addEventListener('click', function () {
                document.dispatchEvent(
                  e.cookieConsent.events.cc_preferencesCenterSavePressed
                );
              }),
              a.appendChild(n, o),
              a.appendChild(t, i),
              a.appendChild(t, n),
              t
            );
          }),
          (e.prototype._getLevelCheckbox = function (e) {
            var t = this,
              i = document.createElement('div');
            if (
              (i.classList.add('cc-custom-checkbox'),
              'strictly-necessary' !== e.id)
            ) {
              var n = t.cookieConsent.userConsent.acceptedLevels,
                o = document.createElement('input');
              o.setAttribute('cookie_consent_toggler', 'true'),
                o.setAttribute('type', 'checkbox'),
                o.setAttribute('class', 'cc-custom-checkbox'),
                o.setAttribute('id', e.id),
                o.setAttribute('name', e.id),
                o.setAttribute('aria-labelledby', e.id + '_label'),
                (r = document.createElement('label')).setAttribute('for', e.id),
                r.setAttribute('id', e.id + '_label'),
                n[e.id]
                  ? (o.setAttribute('checked', 'checked'),
                    o.setAttribute('aria-checked', 'true'),
                    r.setAttribute('class', 'is-active'),
                    (r.innerHTML = t.cookieConsent.i18n.$t('i18n', 'active')))
                  : (o.setAttribute('aria-checked', 'false'),
                    r.setAttribute('class', 'is-inactive'),
                    (r.innerHTML = t.cookieConsent.i18n.$t(
                      'i18n',
                      'inactive'
                    ))),
                o.addEventListener('change', function () {
                  var i = o.checked,
                    n = e.id,
                    a = document.querySelector('label[for="' + n + '"]');
                  t.cookieConsent.log(
                    'User changed cookie level [' + n + '], new status: ' + i,
                    'info'
                  ),
                    document.dispatchEvent(
                      t.cookieConsent.events.cc_userChangedConsent
                    ),
                    !0 === i
                      ? (t.cookieConsent.userConsent.acceptLevel(n),
                        (a.innerHTML = t.cookieConsent.i18n.$t(
                          'i18n',
                          'active'
                        )))
                      : (t.cookieConsent.userConsent.rejectLevel(n),
                        (a.innerHTML = t.cookieConsent.i18n.$t(
                          'i18n',
                          'inactive'
                        )));
                }),
                o.addEventListener('keypress', function (e) {
                  if (' ' === e.key || 'Spacebar' === e.key)
                    switch (o.getAttribute('aria-checked')) {
                      case 'true':
                        o.setAttribute('aria-checked', 'false');
                        break;
                      case 'false':
                        o.setAttribute('aria-checked', 'true');
                    }
                }),
                a.appendChild(i, o),
                a.appendChild(i, r);
            } else {
              var r,
                s = document.createElement('input');
              s.setAttribute('cookie_consent_toggler', 'true'),
                s.setAttribute('type', 'checkbox'),
                s.setAttribute('checked', 'checked'),
                s.setAttribute('aria-checked', 'true'),
                s.setAttribute('disabled', 'disabled'),
                s.setAttribute('class', 'cc-custom-checkbox'),
                s.setAttribute('id', e.id),
                s.setAttribute('name', e.id),
                s.setAttribute('aria-labelledby', e.id + '_label'),
                s.setAttribute('tabindex', '0'),
                (r = document.createElement('label')).setAttribute('for', e.id),
                r.setAttribute('id', e.id + '_label'),
                (r.innerHTML = t.cookieConsent.i18n.$t(
                  'i18n',
                  'always_active'
                )),
                a.appendChild(i, s),
                a.appendChild(i, r);
            }
            return i;
          }),
          e
        );
      })(),
      Q = (function () {
        function e(e) {
          (this.noticeBanner = null),
            (this.noticeBannerOverlay = null),
            (this.noticeBannerExtraCss = []),
            (this.cookieConsent = e),
            this.noticeBannerExtraCss.push(e.colorPalette.getClass());
        }
        return (
          (e.prototype.initNoticeBanner = function () {
            var e, t;
            if (
              (null === this.noticeBanner &&
                (this.noticeBanner = this.createNoticeBanner()),
              (t =
                'afterbegin' ===
                  this.cookieConsent.ownerNoticeBannerAppendContentPosition ||
                'beforeend' ===
                  this.cookieConsent.ownerNoticeBannerAppendContentPosition
                  ? this.cookieConsent.ownerNoticeBannerAppendContentPosition
                  : 'afterbegin'),
              a.appendChild('body', this.noticeBanner, t),
              this.cookieConsent.log('Notice Banner shown ' + t, 'info'),
              document.dispatchEvent(
                this.cookieConsent.events.cc_noticeBannerShown
              ),
              'interstitial' === this.cookieConsent.ownerNoticeBannerType ||
                'standalone' === this.cookieConsent.ownerNoticeBannerType)
            ) {
              var i = document.querySelector('#termsfeed-com---nb'),
                n = i.querySelectorAll(
                  'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                )[0],
                o = i.querySelectorAll(
                  'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                ),
                r = o[o.length - 1];
              document.addEventListener('keydown', function (e) {
                var t, i;
                ('Tab' === e.key || 9 === e.keyCode) &&
                  (e.shiftKey
                    ? document.activeElement === n &&
                      (null === (t = r) || void 0 === t || t.focus(),
                      e.preventDefault())
                    : document.activeElement === r &&
                      (null === (i = n) || void 0 === i || i.focus(),
                      e.preventDefault()));
              }),
                null === (e = n) || void 0 === e || e.focus();
            }
            return !0;
          }),
          (e.prototype.hideNoticeBanner = function () {
            try {
              this.noticeBanner.classList.add('termsfeed-com---is-hidden'),
                this.cookieConsent.log('Notice Banner hidden', 'info');
            } catch (e) {}
          }),
          (e.prototype.createNoticeBanner = function () {
            var e,
              t,
              i = document.createElement('div');
            if (
              (i.classList.add('termsfeed-com---reset'),
              i.classList.add('termsfeed-com---nb'),
              i.setAttribute('id', 'termsfeed-com---nb'),
              i.setAttribute('role', 'dialog'),
              i.setAttribute('aria-modal', 'true'),
              i.setAttribute('aria-labelledby', 'cc-nb-title'),
              i.setAttribute('aria-describedby', 'cc-nb-text'),
              this.noticeBannerExtraCss.length)
            )
              try {
                for (
                  var n = R(this.noticeBannerExtraCss), o = n.next();
                  !o.done;
                  o = n.next()
                ) {
                  var r = o.value;
                  i.classList.add(r);
                }
              } catch (t) {
                e = { error: t };
              } finally {
                try {
                  o && !o.done && (t = n.return) && t.call(n);
                } finally {
                  if (e) throw e.error;
                }
              }
            if (
              (i.classList.add(
                'termsfeed-com---lang-' + this.cookieConsent.i18n.userLang
              ),
              a.appendChild(i, this.createNoticeBannerContent()),
              'interstitial' === this.cookieConsent.ownerNoticeBannerType)
            ) {
              var s = document.createElement('div');
              return (
                s.classList.add('termsfeed-com---nb-interstitial-overlay'),
                a.appendChild(s, i),
                s
              );
            }
            return i;
          }),
          (e.prototype.createNoticeBannerContent = function () {
            var e = this,
              t = document.createElement('div');
            t.classList.add('cc-nb-main-container');
            var i = document.createElement('div');
            i.classList.add('cc-nb-title-container');
            var n = document.createElement('p');
            n.classList.add('cc-nb-title'),
              n.setAttribute('id', 'cc-nb-title'),
              (n.innerText = e.cookieConsent.i18n.$t('i18n', 'nb_title')),
              a.appendChild(i, n);
            var o = document.createElement('div');
            o.classList.add('cc-nb-text-container');
            var r = document.createElement('p');
            r.classList.add('cc-nb-text'),
              r.setAttribute('id', 'cc-nb-text'),
              (r.innerHTML = e.cookieConsent.i18n.$t('i18n', 'nb_text'));
            var s = document.createElement('span');
            s.classList.add('cc-nb-text-urls'), (s.innerHTML = ' ');
            var c = document.createElement('span');
            c.classList.add('cc-nb-text-urls-privacy'),
              c.setAttribute('role', 'link');
            var l = document.createElement('span');
            l.classList.add('cc-nb-text-urls-impressum'),
              l.setAttribute('role', 'link');
            var p = document.createElement('span');
            p.classList.add('cc-nb-text-urls-separator'),
              (p.innerHTML = ' | '),
              e.cookieConsent.noticeBannerInsertLegalUrls &&
                (e.cookieConsent.ownerWebsitePrivacyPolicyUrl &&
                e.cookieConsent.ownerWebsiteImpressumUrl
                  ? a.isValidUrl(
                      e.cookieConsent.ownerWebsitePrivacyPolicyUrl
                    ) &&
                    a.isValidUrl(e.cookieConsent.ownerWebsiteImpressumUrl) &&
                    ((c.innerHTML = e.cookieConsent.i18n.$t(
                      'i18n',
                      'privacy_policy',
                      e.cookieConsent.ownerWebsitePrivacyPolicyUrl
                    )),
                    (l.innerHTML = e.cookieConsent.i18n.$t(
                      'i18n',
                      'impressum',
                      e.cookieConsent.ownerWebsiteImpressumUrl
                    )),
                    a.appendChild(s, c),
                    a.appendChild(c, p),
                    a.appendChild(s, l))
                  : e.cookieConsent.ownerWebsitePrivacyPolicyUrl &&
                    a.isValidUrl(e.cookieConsent.ownerWebsitePrivacyPolicyUrl)
                  ? ((c.innerHTML = e.cookieConsent.i18n.$t(
                      'i18n',
                      'privacy_policy',
                      e.cookieConsent.ownerWebsitePrivacyPolicyUrl
                    )),
                    a.appendChild(s, c))
                  : e.cookieConsent.ownerWebsiteImpressumUrl &&
                    a.isValidUrl(e.cookieConsent.ownerWebsiteImpressumUrl) &&
                    ((l.innerHTML = e.cookieConsent.i18n.$t(
                      'i18n',
                      'impressum',
                      e.cookieConsent.ownerWebsiteImpressumUrl
                    )),
                    a.appendChild(s, l)),
                a.appendChild(r, s)),
              a.appendChild(o, r);
            var d = document.createElement('div');
            d.classList.add('cc-nb-buttons-container');
            var u = document.createElement('button');
            u.classList.add('cc-nb-okagree'),
              u.setAttribute('role', 'button'),
              'express' == e.cookieConsent.ownerConsentType
                ? (u.innerHTML = e.cookieConsent.i18n.$t('i18n', 'nb_agree'))
                : (u.innerHTML = e.cookieConsent.i18n.$t('i18n', 'nb_ok')),
              u.addEventListener('click', function () {
                document.dispatchEvent(
                  e.cookieConsent.events.cc_noticeBannerOkOrAgreePressed
                );
              }),
              a.appendChild(d, u);
            var m = document.createElement('button');
            m.classList.add('cc-nb-reject'),
              m.setAttribute('role', 'button'),
              (m.innerHTML = e.cookieConsent.i18n.$t('i18n', 'nb_reject')),
              m.addEventListener('click', function () {
                document.dispatchEvent(
                  e.cookieConsent.events.cc_noticeBannerRejectPressed
                );
              }),
              'express' == e.cookieConsent.ownerConsentType &&
                !1 === e.cookieConsent.ownerNoticeBannerRejectButtonHide &&
                a.appendChild(d, m);
            var _ = document.createElement('button');
            return (
              _.classList.add('cc-nb-changep'),
              _.setAttribute('role', 'button'),
              (_.innerHTML = e.cookieConsent.i18n.$t('i18n', 'nb_changep')),
              _.addEventListener('click', function () {
                document.dispatchEvent(
                  e.cookieConsent.events.cc_noticeBannerChangePreferencesPressed
                );
              }),
              a.appendChild(d, _),
              a.appendChild(t, i),
              a.appendChild(t, o),
              a.appendChild(t, d),
              t
            );
          }),
          e
        );
      })(),
      X = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return i.noticeBannerExtraCss.push('termsfeed-com---nb-simple'), i;
        }
        return J(t, e), t;
      })(Q),
      ee = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return i.noticeBannerExtraCss.push('termsfeed-com---nb-headline'), i;
        }
        return J(t, e), t;
      })(Q),
      te = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return (
            i.noticeBannerExtraCss.push('termsfeed-com---nb-interstitial'), i
          );
        }
        return J(t, e), t;
      })(Q),
      ie = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return (
            i.noticeBannerExtraCss.push('termsfeed-com---nb-standalone'), i
          );
        }
        return J(t, e), t;
      })(Q),
      ne = (function () {
        function e(e) {
          e.log('ConsentType main class initialized', 'info');
        }
        return (e.prototype.loadInitialCookiesForNewUser = function () {}), e;
      })(),
      oe = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return (i.cookieConsent = t), i;
        }
        return (
          J(t, e),
          (t.prototype.loadInitialCookiesForNewUser = function () {
            this.cookieConsent.log(
              'consentImplied loadInitialCookiesForNewUser triggered',
              'info'
            );
            var e = !1,
              t = !1,
              i = !1;
            if (null !== this.cookieConsent.ownerPageLoadConsentLevels)
              for (var n in this.cookieConsent.ownerPageLoadConsentLevels) {
                var o = this.cookieConsent.ownerPageLoadConsentLevels[n];
                'functionality' == o && (e = !0),
                  'tracking' == o && (t = !0),
                  'targeting' == o && (i = !0);
              }
            else (e = !0), (t = !0), (i = !0);
            this.cookieConsent.javascriptItems.enableScriptsByLevel(
              'strictly-necessary'
            ),
              e
                ? (this.cookieConsent.userConsent.acceptLevel('functionality'),
                  this.cookieConsent.javascriptItems.enableScriptsByLevel(
                    'functionality'
                  ))
                : this.cookieConsent.userConsent.rejectLevel('functionality'),
              t
                ? (this.cookieConsent.userConsent.acceptLevel('tracking'),
                  this.cookieConsent.javascriptItems.enableScriptsByLevel(
                    'tracking'
                  ))
                : this.cookieConsent.userConsent.rejectLevel('tracking'),
              i
                ? (this.cookieConsent.userConsent.acceptLevel('targeting'),
                  this.cookieConsent.javascriptItems.enableScriptsByLevel(
                    'targeting'
                  ))
                : this.cookieConsent.userConsent.rejectLevel('targeting'),
              this.cookieConsent.log(
                'consentImplied loadInitialCookiesForNewUser: strictly-necessary (true), functionality (' +
                  e +
                  '), tracking (' +
                  t +
                  '), targeting (' +
                  i +
                  ')',
                'info'
              );
          }),
          t
        );
      })(ne),
      ae = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return (i.cookieConsent = t), i;
        }
        return (
          J(t, e),
          (t.prototype.loadInitialCookiesForNewUser = function () {
            this.cookieConsent.log(
              'consentExpress loadInitialCookiesForNewUser triggered',
              'info'
            );
            var e = !1,
              t = !1,
              i = !1;
            if (null !== this.cookieConsent.ownerPageLoadConsentLevels)
              for (var n in this.cookieConsent.ownerPageLoadConsentLevels) {
                var o = this.cookieConsent.ownerPageLoadConsentLevels[n];
                'functionality' == o && (e = !0),
                  'tracking' == o && (t = !0),
                  'targeting' == o && (i = !0);
              }
            else (e = !1), (t = !1), (i = !1);
            this.cookieConsent.javascriptItems.enableScriptsByLevel(
              'strictly-necessary'
            ),
              e
                ? (this.cookieConsent.userConsent.acceptLevel('functionality'),
                  this.cookieConsent.javascriptItems.enableScriptsByLevel(
                    'functionality'
                  ))
                : this.cookieConsent.userConsent.rejectLevel('functionality'),
              t
                ? (this.cookieConsent.userConsent.acceptLevel('tracking'),
                  this.cookieConsent.javascriptItems.enableScriptsByLevel(
                    'tracking'
                  ))
                : this.cookieConsent.userConsent.rejectLevel('tracking'),
              i
                ? (this.cookieConsent.userConsent.acceptLevel('targeting'),
                  this.cookieConsent.javascriptItems.enableScriptsByLevel(
                    'targeting'
                  ))
                : this.cookieConsent.userConsent.rejectLevel('targeting'),
              this.cookieConsent.log(
                'consentExpress loadInitialCookiesForNewUser: strictly-necessary (true), functionality (' +
                  e +
                  '), tracking (' +
                  t +
                  '), targeting (' +
                  i +
                  ')',
                'info'
              );
          }),
          t
        );
      })(ne),
      re = (function () {
        function e(e) {
          this.cookieConsent = e;
        }
        return (
          (e.prototype.getClass = function () {
            return 'termsfeed-com---palette-light';
          }),
          e
        );
      })(),
      se = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return (i.cookieConsent = t), i;
        }
        return (
          J(t, e),
          (t.prototype.getClass = function () {
            return 'termsfeed-com---palette-dark';
          }),
          t
        );
      })(re),
      ce = (function (e) {
        function t(t) {
          var i = e.call(this, t) || this;
          return (i.cookieConsent = t), i;
        }
        return (
          J(t, e),
          (t.prototype.getClass = function () {
            return 'termsfeed-com---palette-light';
          }),
          t
        );
      })(re),
      le = (function () {
        function e(e) {
          (this.USER_TOKEN_COOKIE_NAME = 'cookie_consent_user_consent_token'),
            (this.cookieConsent = e),
            this.initUserConsentToken();
        }
        return (
          (e.prototype.initUserConsentToken = function () {
            var e = F('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
              t = F('abcdefghijklmnopqrstuvwxyz'),
              i = F('0123456789'),
              n = F(e, i, t);
            (this.cookieConsent.userConsentToken =
              a.getCookie(this.USER_TOKEN_COOKIE_NAME) ||
              this.cookieConsent.configUserConsentToken ||
              (function (e, t) {
                return F(Array(t))
                  .map(function (t) {
                    return e[(Math.random() * e.length) | 0];
                  })
                  .join('');
              })(n, 12)),
              a.setCookie(
                this.USER_TOKEN_COOKIE_NAME,
                this.cookieConsent.userConsentToken,
                this.cookieConsent.ownerDomain,
                this.cookieConsent.cookieSecure,
                this.cookieConsent.cookieDuration
              );
          }),
          e
        );
      })(),
      pe = (function () {
        function e(e) {
          switch (
            ((this.configUserConsentToken = void 0),
            (this.userConsentToken = void 0),
            (this.debug = !1),
            (this.ownerConsentType = e.consent_type || 'express'),
            (this.ownerWebsiteName = e.website_name || ''),
            (this.ownerWebsitePrivacyPolicyUrl =
              e.website_privacy_policy_url || null),
            (this.ownerColorPalette = e.palette || 'light'),
            (this.ownerSiteLanguage = e.language || 'en'),
            (this.ownerDomain = e.cookie_domain || ''),
            (this.ownerWebsiteImpressumUrl = e.website_impressum_url || null),
            (this.noticeBannerInsertLegalUrls =
              e.notice_banner_insert_legal_urls || !1),
            (this.cookieSecure = e.cookie_secure || !1),
            (this.ownerPageLoadConsentLevels =
              e.page_load_consent_levels || null),
            (this.ownerNoticeBannerType = e.notice_banner_type || 'headline'),
            (this.ownerNoticeBannerRejectButtonHide =
              e.notice_banner_reject_button_hide || !1),
            (this.ownerNoticeBannerAppendContentPosition =
              e.notice_banner_append || 'afterbegin'),
            (this.ownerOpenPreferencesCenterSelector =
              e.open_preferences_center_selector || '#open_preferences_center'),
            (this.ownerPreferencesCenterCloseButtonHide =
              e.preferences_center_close_button_hide || !1),
            (this.pageRefreshConfirmationButtons =
              e.page_refresh_confirmation_buttons || !1),
            (this.configUserConsentToken = e.user_consent_token || null),
            (this.cookieDuration = parseInt(e.cookie_duration || 3650)),
            (this.isDemo = 'true' == e.demo),
            (this.debug = 'true' == e.debug),
            this.ownerConsentType)
          ) {
            default:
            case 'express':
              this.consentType = new ae(this);
              break;
            case 'implied':
              (this.consentType = new oe(this)),
                (this.userConsentTokenClass = new le(this));
          }
          switch (this.ownerColorPalette) {
            default:
            case 'dark':
              this.colorPalette = new se(this);
              break;
            case 'light':
              this.colorPalette = new ce(this);
          }
          switch (this.ownerNoticeBannerType) {
            default:
            case 'simple':
              this.noticeBannerContainer = new X(this);
              break;
            case 'headline':
              this.noticeBannerContainer = new ee(this);
              break;
            case 'interstitial':
              this.noticeBannerContainer = new te(this);
              break;
            case 'standalone':
              this.noticeBannerContainer = new ie(this);
          }
          (this.events = new K()),
            (this.eventsListeners = new H(this)),
            (this.customerCallbacks = new G(this, e.callbacks)),
            (this.i18n = new M(this)),
            (this.cookieLevels = new Z(this)),
            (this.userConsent = new V(this)),
            (this.javascriptItems = new $(this)),
            (this.preferencesCenterContainer = new Y(this)),
            null !== this.ownerOpenPreferencesCenterSelector &&
              this.preferencesCenterContainer.listenToUserButtonToOpenPreferences(
                this.ownerOpenPreferencesCenterSelector
              ),
            !0 === this.userConsent.userAccepted
              ? (this.userConsent.loadAcceptedCookies(),
                !0 === this.isDemo &&
                  this.noticeBannerContainer.initNoticeBanner())
              : (this.noticeBannerContainer.initNoticeBanner(),
                this.consentType.loadInitialCookiesForNewUser());
        }
        return (
          (e.prototype.log = function (e, t, i) {
            if ((void 0 === i && (i = 'log'), !0 === this.debug)) {
              var n = new Date().toISOString();
              console.log('[' + n + '][' + t + '] ' + e);
            }
          }),
          (e.prototype.openPreferencesCenter = function () {
            this.preferencesCenterContainer.showPreferencesCenter();
          }),
          e
        );
      })(),
      de = function (e) {
        return (
          (o = new pe(e)),
          (window.cookieconsent.openPreferencesCenter = function () {
            o.openPreferencesCenter();
          }),
          o
        );
      };
  }
]);
