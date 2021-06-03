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
			<div class="field">
				<p class="control has-icons-left">
					<input class="input" type="text" placeholder="Buscar" onkeyup="buscar_archivos(this);" />
					<span class="icon is-small is-left "><i class="fa fa-search"></i></span>
					<i id="btn-loading" class="button is-loading" style="display: none;"></i>
					<i id="btnFileLoading" class="button is-loading" style="display: none;"></i>
				</p>
			</div>
			<div class="columns">
				<div class="column is-hidden-mobile is-one-third is-warning">
					<nav id="PanelCarpetas" class="panel">
						<p class="panel-heading">Carpetas</p>
						<a class="panel-block is-active">
							<span class="panel-icon"><i class="fa fa-folder"></i></span> No hay elementos que mostrar
						</a>
					</nav>
				</div>
				<div class="column is-two-third">
					<nav id="PanelArchivos" class="panel">
						<p class="panel-heading">Archivos</p>
						<label id="dirname"></label>
						<a class="panel-block "><span class="panel-icon"><i class="fa fa-cube"></i></span> No hay elementos que mostrar</a>
					</nav>
					<nav id="PanelSearchFile" class="panel"> </nav>
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
		let namecolmns = "Legislación por tema";
		let searching = false;
		let searchingfile = false;

		const clsfile = (filename) => {
			let name = "";

			name = filename.replace(".pdf","");
			
			return name;
		}

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
				let divloading = document.getElementById('btn-loading');
				divloading.style = "display: normal;";
				const config = {url: '/search.php', method: 'post', data: {dir: "", buscar: search_file }, baseURL: 'bin/', headers: {'Content-Type': 'application/json','X-Requested-With': 'XMLHttpRequest', "Access-Control-Allow-Origin" : "*"}};
				axios
				.request(config)
				.then(function(response) {
					resolve(response.data);
					divloading.style = "display: none;";
				})
				.catch(function(error) {
					reject(error);
					divloading.style = "display: none;";
				})
			});
		}

		const load_search_to_file = async () => {
			return await new Promise((resolve, reject) => {
				let divloading = document.getElementById('btnFileLoading');
				divloading.style = "display: normal;";
				const config = {url: '/searchfile.php', method: 'post', data: {dir: "", buscar: search_file }, baseURL: 'bin/', headers: {'Content-Type': 'application/json','X-Requested-With': 'XMLHttpRequest', "Access-Control-Allow-Origin" : "*"}};
				axios
				.request(config)
				.then(function(response) {
					resolve(response.data);
					divloading.style = "display: none;";
				})
				.catch(function(error) {
					reject(error);
					divloading.style = "display: none;";
				})
			});
		}
		const buscar_archivos = async (element) => {
			if(!searching){
				searching = true;
				search_file = element.value;
				let origen="";
				let Rebeca = await load_search();
				let panelArchivos = document.getElementById('PanelArchivos');
				let listaArchivos = '<p class="panel-heading">Resultado de Busqueda<span></p>';
				// listaArchivos += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search_file+'" onkeyup="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';

				for(archivo in Rebeca.folder){
					if(Rebeca.folder[archivo].isdir){
						listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.folder[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.folder[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
					}else{
						listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+archivo+'" href="'+Rebeca.folder[archivo].url+'"><span class="panel-icon"><i class="fa fa-file-pdf"></i></span>' + clsfile(Rebeca.folder[archivo].name) + ' </a>';
					}
				}
				dir_show = carpeta;
				panelArchivos.innerHTML = listaArchivos;
				searching = false;
			}

			searchingfile =false;
			if(!searchingfile){
				if(searfile.length > 5 ){
					searchingfile = true;
					//search_file = element.value;
					let Rebeca2 = await load_search_to_file();
					let panelArchivos2 = document.getElementById('PanelSearchFile');
					let listaArchivos2 = ''; 
					for(archivo2 in Rebeca2.folder){
						//if(Rebeca.folder[archivo].isdir){
						//	listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.folder[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.folder[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
						//}else{
							listaArchivos2 += '<a class="panel-block" target="_blank" id="file_search'+archivo2+'" href="'+Rebeca2.folder[archivo2].url+'"><span class="panel-icon"><i class="fa fa-file-pdf"></i></span>' + clsfile(Rebeca2.folder[archivo2].name) + ' </a>';
						//}
					}
					//dir_show = carpeta;
					panelArchivos2.innerHTML = listaArchivos2;
					searchingfile = false;
				}
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
				if(lsubcarpeta != ".."){
					let icon = (lista_subcarpetas[subcarpeta] != "" && lista_subcarpetas[subcarpeta] != "files" ? "fa-folder" : "fa-home");
					let ldescripcion = (lista_subcarpetas[subcarpeta] != "" && lista_subcarpetas[subcarpeta] != "files" ? lista_subcarpetas[subcarpeta] : "");
					let link = '<a class="button is-small" onclick="mostrar_archivos(\''+lsubcarpeta+'\',\'\')"><span class="icon is-small"><i class="fa '+icon+'"></i></span> <span>'+ldescripcion+'</span></a>';
					lcarpeta += link;
				}
			}

			let listCarpetas = '<p class="panel-heading">'+namecolmns+'</p>';
			//listCarpetas += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search+'" onkeypress="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';
			let Tcarpetas = document.getElementById('PanelCarpetas');

			for(carpeta in Rebeca.folder){
				if(Rebeca.folder[carpeta].name != "NORMAS DE USO COMUN"){
					listCarpetas += '<a class="panel-block" id="capeta_'+origen+carpeta+'" onclick="mostrar_archivos(\''+Rebeca.folder[carpeta].url+'\', \''+origen+carpeta+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.folder[carpeta].name + ' <div class="subcarpetas" id="sub'+origen+carpeta+'"></div> </a>';
				}
			}
			Tcarpetas.innerHTML = listCarpetas;

			let listaArchivos = '<p class="panel-heading">'+lcarpeta+'<span></p>';
			//listaArchivos += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search_file+'" onkeyup="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';

			for(archivo in Rebeca.files){
				if(Rebeca.files[archivo].isdir){
					if(Rebeca.folder[carpeta].name != "NORMAS DE USO COMUN "){
						listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.files[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.files[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
					}
				}else{
					listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+archivo+'" href="'+Rebeca.files[archivo].url+'"><span class="panel-icon"><i class="fa fa-file-pdf"></i></span>' + clsfile(Rebeca.files[archivo].name) + ' </a>';
				}
			}

			dir_show = carpeta;

			panelArchivos.innerHTML = listaArchivos;

			//console.log(Rebeca.result);
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
			let listCarpetas = '<p class="panel-heading">'+namecolmns+'</p>';
			//listCarpetas += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search+'" onkeypress="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';
			let Tcarpetas = document.getElementById('PanelCarpetas');

			let panelArchivos = document.getElementById('PanelArchivos');
			let listaArchivos = '<p class="panel-heading"><a class="button is-small" onclick="mostrar_archivos(\'files\',\'\')"><span class="panel-icon"><i class="fa fa-home"></i></span></a><span></p>';
			for(carpeta in Rebeca.folder){
				//alert("|" + Rebeca.folder[carpeta].name+"|");
				if(Rebeca.folder[carpeta].name != "NORMAS DE USO COMUN"){
					listCarpetas += '<a class="panel-block" id="capeta_'+origen+carpeta+'" onclick="mostrar_archivos(\''+Rebeca.folder[carpeta].url+'\', \''+origen+carpeta+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.folder[carpeta].name + ' <div class="subcarpetas" id="sub'+origen+carpeta+'"></div> </a>';
				}
			}
			Tcarpetas.innerHTML = listCarpetas;

			//listaArchivos += '<div class="panel-block"><p class="control has-icons-left"><input class="input is-small" type="text" placeholder="search" value="'+search_file+'" onkeyup="buscar_archivos(this);"><span class="icon is-small is-left"><i class="fas fa-search" aria-hidden="true"></i></span></p></div>';
			/*
			for(archivo in Rebeca.files){
				if(Rebeca.files[archivo].isdir){
					listaArchivos += '<a class="panel-block" id="archivo_'+origen+archivo+'" onclick="mostrar_archivos(\''+Rebeca.files[archivo].url+'\', \''+origen+archivo+'\')"><span class="panel-icon"><i class="fa fa-folder"></i></span>' + Rebeca.files[archivo].name + ' <div class="subcarpetas" id="sub'+origen+archivo+'"></div> </a>';
				}else{
					listaArchivos += '<a class="panel-block" target="_blank" id="file_'+origen+archivo+'" href="'+Rebeca.files[archivo].url+'"><span class="panel-icon"><i class="fa fa-file"></i></span>' + Rebeca.files[archivo].name + ' </a>';
				}
			}
			*/
			panelArchivos.innerHTML = listaArchivos;
		}
      
    	load_main();
	</script>
</body>
</html>
