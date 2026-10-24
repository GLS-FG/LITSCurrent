{{--
    Carga perezosa de Google Maps (Places) para los modales de direcciones: el script de Google se
    pide hasta la primera vez que alguien lo necesita, no en cada carga de la página.
    Uso: const { PlaceAutocompleteElement } = await loadPlacesLibrary();
    Se puede incluir más de una vez en la misma página (crear y editar): solo se define una vez.
--}}
<script>
    if (!window.loadPlacesLibrary) {
        (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
            key: "{{config('services.google_maps.key')}}",
            v: "weekly",
        });
        window.loadPlacesLibrary = () => google.maps.importLibrary('places');
    }
</script>
