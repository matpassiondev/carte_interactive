mapboxgl.accessToken = 'pk.eyJ1IjoibWF0cGFzc2lvbmRldiIsImEiOiJja2t3ZGdodWUwNTlnMnJvNjNmc2FlcHNmIn0.LL9eetybn5zVyOBPl9AYWg';
var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [5.05, 47.31],
    zoom: 12,
});

// charger la map
map.on('load', function () {
    map.addSource('mapbox-dem', {
        'type': 'raster-dem',
        'url': 'mapbox://mapbox.mapbox-terrain-dem-v1',
        'tileSize': 512,
        'maxzoom': 14,
    });
});

// controles de navigation
map.addControl(new mapboxgl.NavigationControl());

// Ajout de la géolocalisation de l'utilisateur.
map.addControl(
    new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true
        },
        trackUserLocation: true
    })
);


// Géocoder
var geocoder = new MapboxGeocoder({
    accessToken: mapboxgl.accessToken,

    // limiter les résultats à la France
    countries: 'fr',

    
    // la région Côte d'or
    filter: function (item) {
        
        return item.context
            .map(function (i) {
                
                return (
                    i.id.split('.').shift() === 'region' &&
                    i.text === 'Côte-d\'Or'
                );
            })
            .reduce(function (acc, cur) {
                return acc || cur;
            });
    },

    flyTo: {
        bearing: 0,
        speed: 1, 
        curve: 1,
        easing: function (t) {
            return t;
        }
    },
    mapboxgl: mapboxgl
});
map.addControl(geocoder);

// AJAX

// Process de recherche
let nomSoc = new Array();
let lat = new Array();
let lon = new Array();
let nb = 0;

console.log($("#bouton-tourisme"));
$("#bouton-tourisme").click(getInfos);


function getInfos() {
    console.log("fonction");

    $.get('request.php', {'type':'hot'}, function (data) {
console.log(data);

$(data).find("obj").each(function (){


nomSoc[nb] = $(this).find("societe").text();
console.log(nomSoc[nb]);

lat[nb] = $(this).find("lat").text();
console.log(lat[nb]);

lon[nb] = $(this).find("lng").text();
console.log(lon[nb]);


let popup = new mapboxgl.Popup({ offset : 25}).setText(nomSoc[nb]);
 
let markers = new mapboxgl.Marker()
    .setLngLat([lon[nb], lat[nb]])
    .setPopup(popup)
    .addTo(map);

}
);

}
);
}



//================== Local Storage ======================

let nomUser, ageUser, themeUser;


$("#btnPref").click(getPref);

function getPref() {
    nomUser = $("#nomUser").val();
    ageUser = $("#age").val();
    themeUser = $("input[name=theme]").attr('id');

    localStorage.nom = nomUser;
    localStorage.age = ageUser;
    localStorage.theme = themeUser;

}

console.log(localStorage.theme);

if (localStorage.theme == 'dark') {
    console.log('dark');
    $('body').css({'background-color':'darkgrey', 'color':'white'});
} else {
    $('body').css({'background-color':'white', 'color':'black'});
}