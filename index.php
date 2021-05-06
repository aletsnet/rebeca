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
			<div class="columns">
				<div class="column is-hidden-mobile is-one-third">
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
					<button class="delete" onclick="closeModal('modalArchivo')"></button>
				</header>
				<section class="modal-card-body">
				<img src="image/load.gif" /> Cargando ...
				</section>
				<footer class="modal-card-foot">
					
				</footer>
			</div>
		</div>
		<div id="modalArchivo" class="modal">
			<div class="modal-background"></div>
			<div class="modal-card">
				<header class="modal-card-head">
				<p class="modal-card-title">Repositorio</p>
					<button class="delete" onclick="closeModal('modalArchivo')"></button>
				</header>
				<section class="modal-card-body">
					<iframe id="frameArchivo" src="image/load.gif" width="100%" height="600px" title="Repositorio"></iframe>
				</section>
				<footer class="modal-card-foot">
					
				</footer>
			</div>
		</div>
	</div><!--.container-->
	<script>
		let search_file = "";
		let dir_show = "";
		let searching = false;
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

		const load_search = async () => {
			return await new Promise((resolve, reject) => {
				let divmodal = document.getElementById('modal');
				//divmodal.className = "modal is-active";
				const config = {url: '/search.php', method: 'post', data: {dir: dir_show, buscar: search_file }, baseURL: 'bin/', headers: {'Content-Type': 'application/json','X-Requested-With': 'XMLHttpRequest', "Access-Control-Allow-Origin" : "*"}};
				axios
				.request(config)
				.then(function(response) {
					resolve(response.data);
					//divmodal.className = "modal";
				})
				.catch(function(error) {
					reject(error);
					//divmodal.className = "modal";
				})
			});
		}

		const buscar_archivos = async (element) => {
			if(!searching){
				searching = true;
				search_file = element.value;
				let Rebeca = await load_search();

				console.log(Rebeca);
				searching = false;
			}
		}

		const mostrar_archivos = async (carpeta = "", destino = "") => {
			let Rebeca = await load_api(carpeta);
			let origen = carpeta.replace("/", "_");
			let lcarpeta = ""; //carpeta.replace("files",'<a id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\'\', \'\')"><span class="panel-icon"><i class="fa fa-home"></i></span></a>');
			let panelArchivos = document.getElementById('PanelArchivos');
			let capetaactual = document.getElementById('capeta_' + destino);
			let lista_subcarpetas = carpeta.split("/");

			let lsubcarpeta = "";
			for(subcarpeta in lista_subcarpetas){
				lsubcarpeta += (lsubcarpeta!="" ? "/" : "" ) + lista_subcarpetas[subcarpeta];
				let icon = (lista_subcarpetas[subcarpeta] != "" && lista_subcarpetas[subcarpeta] != "files" ? "fa-folder" : "fa-home");
				let ldescripcion = (lista_subcarpetas[subcarpeta] != "" && lista_subcarpetas[subcarpeta] != "files" ? lista_subcarpetas[subcarpeta] : "");
				let link = '<a class="tag is-light " onclick="mostrar_archivos(\''+lsubcarpeta+'\',\'\')"><span class="panel-icon"><i class="fa '+icon+'"></i></span> '+ldescripcion+'</a><span>';
				lcarpeta += link;
			}

			let listaArchivos = '<p class="panel-heading">'+lcarpeta+'<span></p>';
			listaArchivos += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search_file+'" onkeyup="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';

			for(archivo in Rebeca.files){
				if(Rebeca.files[archivo].isdir){
					listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.files[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.files[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
				}else{
					listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+carpeta+'" href="'+Rebeca.files[archivo].url+'"><span class="panel-icon"><i class="fa fa-file"></i></span>' + Rebeca.files[archivo].name + ' </a>';
				}
			}

			dir_show = carpeta;

			panelArchivos.innerHTML = listaArchivos;
		}

		const mostrar_archivo = (archivo = "") => { 
			let modalArchivos = document.getElementById('modalArchivo');
			let frameArchivo = document.getElementById('frameArchivo');
			modalArchivos.className = "modal is-active";
			frameArchivo.src = archivo;
			//console.log(frameArchivo);
		}

		const closeModal = (namemodal = "") => { 
			let modalFrame = document.getElementById(namemodal);
			modalFrame.className = "modal";
		}

		const load_main = async (origen = "") => {
			let Rebeca = await load_api(origen);
			let listCarpetas = '<p class="panel-heading">Carpetas</p>';
			//listCarpetas += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search+'" onkeypress="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';
			let Tcarpetas = document.getElementById('PanelCarpetas');

			let panelArchivos = document.getElementById('PanelArchivos');
			let listaArchivos = '<p class="panel-heading"><a><span class="panel-icon"><i class="fa fa-home"></i></span></a><span></p>';

			for(carpeta in Rebeca.folder){
				listCarpetas += '<a class="panel-block" id="capeta_'+origen+carpeta+'" onclick="mostrar_archivos(\''+Rebeca.folder[carpeta].url+'\', \''+origen+carpeta+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.folder[carpeta].name + ' <div class="subcarpetas" id="sub'+origen+carpeta+'"></div> </a>';
			}

			Tcarpetas.innerHTML = listCarpetas;

			listaArchivos += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search_file+'" onkeyup="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';

			for(archivo in Rebeca.files){
				if(Rebeca.files[archivo].isdir){
					listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.files[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.files[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
				}else{
					listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+archivo+'" href="'+Rebeca.files[archivo].url+'"><span class="panel-icon"><i class="fa fa-file"></i></span>' + Rebeca.files[archivo].name + ' </a>';
				}
			}

			panelArchivos.innerHTML = listaArchivos;

		}
      
    	load_main();
	</script>
</body>
</html>
