<script>
    const API_KEY = 'b666a70a823c02390f77259663456345'; // Clave publica
    const tituloInput = document.getElementById('titulo_input');
    const suggestionsBox = document.getElementById('suggestions');
    const imagenInput = document.getElementById('imagen_input');
    const tipoContenido = document.getElementById('tipo_contenido');

    if(tituloInput){
        tituloInput.addEventListener('input', async function() {
            const query = this.value;
            if (query.length < 3) { suggestionsBox.style.display = 'none'; return; }
            const type = (tipoContenido.value === 'pelicula') ? 'movie' : 'tv';
            
            try {
                const res = await fetch(`https://api.themoviedb.org/3/search/${type}?api_key=3fd2be6f0c70a2a598f084ddfb75487c&language=es-ES&query=${query}`);
                const data = await res.json();
                suggestionsBox.innerHTML = '';
                if (data.results && data.results.length > 0) {
                    suggestionsBox.style.display = 'block';
                    data.results.slice(0, 5).forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'sugg-item';
                        const title = item.title || item.name;
                        const year = (item.release_date || item.first_air_date || '').split('-')[0];
                        const img = item.poster_path ? `https://image.tmdb.org/t/p/w92${item.poster_path}` : '';
                        
                        div.innerHTML = `<img src="${img}" style="width:30px; margin-right:10px;"><span>${title} (${year})</span>`;
                        div.addEventListener('click', () => {
                            tituloInput.value = title;
                            if(item.poster_path) imagenInput.value = `https://image.tmdb.org/t/p/w500${item.poster_path}`;
                            suggestionsBox.style.display = 'none';
                        });
                        suggestionsBox.appendChild(div);
                    });
                }
            } catch (e) { console.log(e); }
        });
        document.addEventListener('click', (e) => { if(e.target !== tituloInput) suggestionsBox.style.display='none'; });
    }
</script>
