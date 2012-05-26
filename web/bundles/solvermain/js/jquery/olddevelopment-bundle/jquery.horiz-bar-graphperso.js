/*
 * Horizontal Bar Graph for jQuery
 * version 0.1a
 *
 * http://www.dumpsterdoggy.com/plugins/horiz-bar-graph
 *
 * Copyright (c) 2009 Chris Missal
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
(function($) {
    $.fn.horizontalBarGraph = function(options) {

        var opts = $.extend({}, $.fn.horizontalBarGraph.defaults, options);

        this.children("dt,dd").each(function(i) {

            var el = $(this);

            if(el.is("dt")) {
                el.css({
                    display: "block",
                    float: "left",
                    clear: "left",
                    visibility: "hidden",
                    width: "10px",
                    overflow: "hidden"                    
                }).addClass("hbg-label");
                return;
            }
            else {

                (isTitleDD(el) && opts.hasTitles ? createTitle : createBar)(el, opts);
            }
            setBarHover(el, opts);
        });

        tryShowTitle(this);

        //if(opts.animated) {
        //    createShowButton(opts, this).insertBefore(this);
        // }
        if(opts.colors.length) {
            setColors(this.children("dd"), opts);
        }
        if(opts.hoverColors.length) {
            setHoverColors(this.children("dd"), opts);
        }


        scaleGraph(this);               //mise à l'échelle des barres
        this.children("dt").remove();   //vire les labels (premiere colonne du tableau)

        return this;
    };

    function scaleGraph(graph) {
        var maxWidth = 0;
        graph.children("dt").each(function() {
            maxWidth = Math.max($(this).width(), maxWidth);
        }).css({
            width: maxWidth+"px"
        });
    }

    function setBarHover(bar, opts) {
        bar.hover(function() {
            bar.addClass("hbg-bar-hover");
        }, function() {
            bar.removeClass("hbg-bar-hover");
        });
    }

    function createShowButton(opts, graph) {
        var button = $("<span />").text(opts.button).addClass("hbg-show-button");
        button.click(function() {
            graph.children("dd").show('slow', function() {
                button.fadeOut('normal');
            });
        });
        return button;
    }

    function createBar(e, opts) {
        var chaine=e.prev().text();
        var posseparateur=chaine.lastIndexOf("-");  //position ud sépérateur "-" qui sépare l'id de la solution, et son titre (utile pour créer le lien http)
        var id=chaine.substring(0,posseparateur);
        var titre=chaine.substring(posseparateur+1,chaine.lenght);
        var valeur=new Number(e.text()).toFixed(2);        //valeur numérique affichée

        var disptext='<a href="affichesolution.php?id='+id+'">'+titre+" ("+valeur+" %)"+'</a>'

        e.css({
            clear: "left",
            overflow: "visible",
            marginLeft: e.prev().is("dt") ? "5px" : "0px",
            width: Math.floor(valeur/opts.interval)+"px",    //largeur calculée en fonction du pourcentage affiché            
            height: "25px"
        });
        e.html($("<span/>").html(disptext).addClass("hbg-value"));
        applyOptions(e, opts);
    }

    function createTitle(e, opts) {
        var title = e.text();
        e.prev().attr("title", title);
        e.remove();
    }

    function tryShowTitle(graph) {
        var title = graph.attr("title");
        if(title) {
            $("<div/>").text(title).addClass("hbg-title").insertBefore(graph);
            graph.css({
                overflow: "hidden"
            });
        }
    }

    function setColors(bars, opts) {
        var i = 0;
        bars.each(function() {
            var c = i++ % opts.colors.length;
            $(this).css({
                backgroundColor: opts.colors[c]
            });
        });
    }

    function setHoverColors(bars, opts) {
        var i = 0;
        bars.each(function(i) {
            var bar = $(this);
            var c = bar.css("background-color");
            var hc = opts.hoverColors[i++ % opts.hoverColors.length];
            bar.hover(function() {
                $(this).css({
                    backgroundColor: hc
                });
            }, function() {
                $(this).css({
                    backgroundColor: c
                });
            });
        });
    }

    function applyOptions(e, opts) {
        e.css({
            float: "left"
        }).addClass("hbg-bar");
        if(opts.animated) {
            e.hide();
        }
    }

    function isTitleDD(e) {
        return (e.is(":even") && e.prev().is("dd"));
    }

    $.fn.horizontalBarGraph.defaults = {
        interval: 1,
        hasTitles: false,
        animated: false,
        button: 'Show Values',
        colors: [],
        hoverColors: []
    };

})(jQuery);
