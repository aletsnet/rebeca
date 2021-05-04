<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Repositorio</title>
	<link rel="stylesheet" href="css/bulma.min.css">
	<link rel="stylesheet" href="css/custom.css">
	<script src="js/axios.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"></script>
</head>
<body>
	<div class="container">
		<section class="hero">
			<h3 class="title">Respositorio</h3>
			<h4 class="subtitle">Herramienta de publicacion de archivos</h4>
			<hr>
			<div class="columns">
				<div class="column is-one-third">
					<nav id="PanelCarpetas" class="panel">
						<p class="panel-heading">Carpetas</p>
						<a class="panel-block is-active">
							<span class="panel-icon"><i class="fa fa-folder"></i></span> No hay elementos que mostrar
						</a>
					</nav>
				</div>
				<div class="column is-two-third">
					<nav id="PanelArchivos" class="panel">
						<p class="panel-heading">Archivos</p> <label id="dirname"></label>
						<a class="panel-block ">
							<span class="panel-icon"><i class="fa fa-cube"></i></span> No hay elementos que mostrar
						</a>
					</nav>
				</div>
			</div>
			<hr>
			</div>
		</section>
		<div id="modal" class="modal">
			<div class="modal-background"></div>
			<div class="modal-card">
				<header class="modal-card-head">
				<p class="modal-card-title">Repositorio</p>
					<button class="delete" aria-label="close"></button>
				</header>
				<section class="modal-card-body">
				<img src="image/load.gif" /> Cargando ...
				</section>
				<footer class="modal-card-foot">
					
				</footer>
			</div>
		</div>
	</div><!--.container-->
	<script>
		const load_api = async (directorio) => {
			return await new Promise((resolve, reject) => {
				let divmodal = document.getElementById('modal');
				divmodal.className = "modal is-active";
				const config = {url: '/show.php', method: 'post', data: {dir: directorio }, baseURL: 'bin/', headers: {'Content-Type': 'application/json','X-Requested-With': 'XMLHttpRequest', "Access-Control-Allow-Origin" : "*"}};
				axios
				.request(config)
				.then(function(response) {
					resolve(response.data);
					divmodal.className = "modal";
				})
				.catch(function(error) {
					reject(error);
					divmodal.className = "modal";
				})
			});
		}

		const mostrar_archivos = async (carpeta = "", destino = "") => {
			let Rebeca = await load_api(carpeta);
			let origen = carpeta.replace("/", "_");
			let panelArchivos = document.getElementById('PanelArchivos');
			let capetaactual = document.getElementById('capeta_' + destino);
			let listaArchivos = '<p class="panel-heading">'+carpeta+'<span></p>';
			
			for(archivo in Rebeca.files){
				if(Rebeca.files[archivo].isdir){
					listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.files[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.files[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
				}else{
					listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+carpeta+'" href="'+Rebeca.files[archivo].url+'"><span class="panel-icon"><i class="fa fa-file"></i></span>' + Rebeca.files[archivo].name + ' </a>';
				}
			}

			panelArchivos.innerHTML = listaArchivos;
		}

		const load_main = async (origen = "") => {
			let Rebeca = await load_api(origen);
			let listCarpetas = '<p class="panel-heading">Carpetas</p>';
			let Tcarpetas = document.getElementById('PanelCarpetas');

			let panelArchivos = document.getElementById('PanelArchivos');
			let listaArchivos = '<p class="panel-heading">Files<span></p>';

			for(carpeta in Rebeca.folder){
				listCarpetas += '<a class="panel-block" id="capeta_'+origen+carpeta+'" onclick="mostrar_archivos(\''+Rebeca.folder[carpeta].url+'\', \''+origen+carpeta+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.folder[carpeta].name + ' <div class="subcarpetas" id="sub'+origen+carpeta+'"></div> </a>';
			}

			Tcarpetas.innerHTML = listCarpetas;

			for(archivo in Rebeca.files){
				if(Rebeca.files[archivo].isdir){
					listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.files[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.files[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
				}else{
					listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+carpeta+'" href="'+Rebeca.files[archivo].url+'"><span class="panel-icon"><i class="fa fa-file"></i></span>' + Rebeca.files[archivo].name + ' </a>';
				}
			}

			panelArchivos.innerHTML = listaArchivos;

		}
      
    	load_main();
	</script>
</body>
</html>