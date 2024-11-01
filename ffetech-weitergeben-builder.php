<?php

class Weitergeben_Builder {

    function enqueue_imports() {

        wp_enqueue_script ( 'react', plugin_dir_url( __FILE__ ) . 'react.min.js' );
        wp_enqueue_script ( 'react-dom', plugin_dir_url( __FILE__ ) . 'react-dom.min.js'  );
        wp_enqueue_script ( 'babel', plugin_dir_url( __FILE__ ) . 'babel.min.js'  );
    }

    function get_css($maxwidth = 250, $imgheight = 250)
    {
        return <<<RESULT
    <style>

        .weitergeben_form {
            display: block;
            max-width: {$maxwidth}px;
        }

        .weitergeben_form .form_row {
            padding: 4px;
            display: grid !important;
            grid-template-columns: 80px auto;
        }

        .weitergeben_form select {
            height: 30px;
        }

        .weitergeben_form input {
            min-width: 120px;
            height: 30px;
        }

        .hidden {
            display: none;
        }

        .weitergeben_result {
            margin-top: 20px;
        }

        .weitergeben_result .item {
            margin-bottom: 10px;
        }

        .weitergeben_result .imgcontainer {
            width: 100%;
            max-width: {$maxwidth}px;
            height: {$imgheight}px;
            border: 1px solid silver;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .weitergeben_result img {
            max-height: 100%;
            max-width: 100%;
        }

        .weitergeben_nav:hover {
            cursor: pointer;
            color: black;
        }
    </style>
RESULT;
    }

    function get_scripts($extsrcs)
    {
        return <<<RESULT

    <script type="text/babel">

        function Weitergeben_RenderOffers(offers, fallback, hasPrior, hasNext) {

            // console.log("render offers", offers);

            if (offers.length > 0) {

                ReactDOM.render(
                    (
                        <div>
                            
                            {offers.map(item => (
                            <div key={item.id} class="item">
                                <a href={item.link} target="_blank"><div class="imgcontainer"><img src={Array.isArray(item.pictures) ? item.pictures[0].url : item.pictures.url} alt={item.amount_remaining + "x gebraucht " + item.item + " in " + item.zip + " " + item.town}/></div></a>
                                {item.amount_remaining}x <a href={item.link} target="_blank">gebraucht {item.item} <br /> in {item.zip} {item.town}</a>
                                {item.price > 0 ? (<div>Preis: {item.price} €</div>) : (<div>Preis: kostenfrei o. einstellig</div>)}
                            </div>
                            ))}

                            { hasPrior && (<span class="weitergeben_nav weitergeben_prior" onClick={Weitergeben_Prior}>&#171; zurück | </span>) }
                            { hasNext && (<span class="weitergeben_nav weitergeben_next" onClick={Weitergeben_Next}>weiter &#187;</span>) }

                        </div>
                    ),
                    document.getElementById('weitergeben_result_items')
                );
            }
            else
            {
                ReactDOM.render(
                    (
                        <div>
                        <hr />
                        <div dangerouslySetInnerHTML={{__html: fallback}}></div>

                        <hr />
                        </div>
                    ),
                    document.getElementById('weitergeben_result_items')
                );
            }
        }

    </script>

    <script>

        var weitergeben_page = 0;
        var weitergeben_limit = 1;
        var weitergeben_offers = [];
        var weitergeben_offers_page = [];

        function Weitergeben_ShowForm(visible) {
            
            if (visible) {
                document.getElementById("weitergeben_form").classList.remove("hidden");
                document.getElementById("weitergeben_form_search").classList.add("hidden");
            }
            else { 
                document.getElementById("weitergeben_form").classList.add("hidden");
                document.getElementById("weitergeben_form_search").classList.remove("hidden");
            }
        }

        function Weitergeben_LoadOffers() {

            weitergeben_page = -1;
            weitergeben_limit = Number(document.getElementById('weitergeben_limit').value);
            Weitergeben_RenderOffers([], "Suche Einträge ...");
            Weitergeben_ShowForm(false);

            var query = "incl_ext_srcs={$extsrcs}&country=" + document.getElementById('weitergeben_country').value + "&zip=" + document.getElementById('weitergeben_zip').value + "&radius=" + document.getElementById('weitergeben_radius').value + "&categories=" + document.getElementById('weitergeben_categories').value;
            var url = "https://weitergeben.org/wp-json/wgorg/v1/infosystem/get-offers?" + query;
            
            console.log("request", url);
            
            var request = new XMLHttpRequest(); // a new request

            request.ontimeout = function () {
                console.error("The request for " + url + " timed out.");
            };

            request.onload = function() {
                if (request.readyState === 4) {
                    if (request.status === 200) {

                        weitergeben_offers = JSON.parse(request.responseText);
                        // console.log("loaded offers", weitergeben_offers);

                        // shuffle
                        let counter = weitergeben_offers.length;
                        while (counter > 0) {
                            let index = Math.floor(Math.random() * counter);
                            counter--;
                            let temp = weitergeben_offers[counter];
                            weitergeben_offers[counter] = weitergeben_offers[index];
                            weitergeben_offers[index] = temp;
                        }

                        Weitergeben_ShowForm(weitergeben_offers.length == 0);

                        Weitergeben_Next();

                    } else {
                        console.error(request.statusText);
                        weitergeben_offers = [];
                        Weitergeben_ShowForm(true);
                        Weitergeben_Next();
                    }
                }
            };

            request.open("GET", url, true);
            request.send(null);
        }

        function Weitergeben_Prior() {

            if (weitergeben_page <= 0)
                weitergeben_page = weitergeben_offers.length / weitergeben_limit - 1;
            else
                weitergeben_page--;
            
            weitergeben_offers_page = weitergeben_offers.slice(weitergeben_page * weitergeben_limit, weitergeben_page * weitergeben_limit + weitergeben_limit);
            
            Weitergeben_RenderOffers(weitergeben_offers_page, "Keine Einträge gefunden ...", weitergeben_offers.length / weitergeben_limit > 1, weitergeben_offers.length / weitergeben_limit > 1);
        }

        function Weitergeben_Next() {

            weitergeben_page++;
            weitergeben_offers_page = weitergeben_offers.slice(weitergeben_page * weitergeben_limit, weitergeben_page * weitergeben_limit + weitergeben_limit);
            
            if (weitergeben_offers.length > 0 && weitergeben_offers_page.length == 0)
            {
                weitergeben_page=0;
                weitergeben_offers_page = weitergeben_offers.slice(weitergeben_page * weitergeben_limit, weitergeben_page * weitergeben_limit + weitergeben_limit);
            }

            Weitergeben_RenderOffers(weitergeben_offers_page, 'Wohin mit alten Möbeln?<br/>Die Alternative zum Sperrmüll?<br/><a href="https://weitergeben.org/moebel-abgeben/" target="_blank">--&gt;Hier klicken&lt;--</a>', weitergeben_offers.length / weitergeben_limit > 1, weitergeben_offers.length / weitergeben_limit > 1);
        }

        window.addEventListener("load", Weitergeben_LoadOffers, false); 
      
    </script>

RESULT;
    }

    function get_result()
    {
        return <<<RESULT

    <div class="weitergeben_result">
        <div id="weitergeben_result_items"></div>
    </div>

RESULT;
    }

    function get_form($country, $zip, $radius = 50, $category="Alle", $limit=1)
    {
        $value = function ($value, $selected)
        {
            return 'value="' . $value . '"' . ($value == $selected ? ' selected' : '');
        };

        return <<<RESULT

    <p>
    <div id="weitergeben_form_search" class="weitergeben_form hidden">
        <span class="weitergeben_nav" onclick="Weitergeben_ShowForm(true)">Suche ändern</span>
    </div>

    <div id="weitergeben_form" class="weitergeben_form hidden">

        <div class="form_row">
        <span>Land</span>
        <select id="weitergeben_country">
            <option {$value("DE", $country)}>Deutschland</option>
            <option {$value("AT", $country)}>Österreich</option>
            <option {$value("CH", $country)}>Schweiz</option>
        </select>
        </div>

        <div class="form_row">
        <span>PLZ</span>
        <input id="weitergeben_zip" type="text" value="{$zip}"/>
        </div>

        <div class="form_row">
        <span>Radius</span>
        <select id="weitergeben_radius">
            <option {$value("10" , $radius)}>10 km</option>
            <option {$value("25" , $radius)}>25 km</option>
            <option {$value("50" , $radius)}>50 km</option>
            <option {$value("150", $radius)}>150 km</option>
            <option {$value("0"  , $radius)}>überall</option>
        </select>
        </div>
    
        <div class="form_row">
        <span>Kategorie</span>
        <select id="weitergeben_categories">
            <option {$value("Alle"       , $category)}>Alle</option>
            <option {$value("Büro"       , $category)}>Büro</option>
            <option {$value("Garten"     , $category)}>Garten</option>
            <option {$value("Gastronomie", $category)}>Gastronomie</option>
            <option {$value("Gesundheit" , $category)}>Gesundheit</option>
            <option {$value("Kinder"     , $category)}>Kinder</option>
            <option {$value("Laden"      , $category)}>Laden</option>
            <option {$value("Lager"      , $category)}>Lager</option>
            <option {$value("Schule"     , $category)}>Schule</option>
            <option {$value("Sport"      , $category)}>Sport</option>
            <option {$value("Wohnung"    , $category)}>Wohnung</option>
            <option {$value("Werkstatt"  , $category)}>Werkstatt</option>
        </select>
        </div>

        <div class="form_row">
        <span>Anzahl</span>
        <select id="weitergeben_limit">
            <option {$value("1" , $limit)}>1</option>
            <option {$value("2" , $limit)}>2</option>
            <option {$value("5" , $limit)}>5</option>
            <option {$value("10", $limit)}>10</option>
        </select>
        </div>

        <div class="form_row">
            <span></span>
            <button onclick="Weitergeben_LoadOffers()">suchen</button>
        </div>

    </div>
    </p>

RESULT;
    }

    function get_footer()
    {
        return <<<RESULT

    <div class="weitergeben_footer">
        <a href="https://weitergeben.org/moebel-abgeben/">eigene Möbel abgeben &#187;</a><br>
        <a href="https://weitergeben.org/moebel-anfrage-formular/">Möbel-Retter-Newsletter &#187;</a>
    </div>

RESULT;
    }
}
?>