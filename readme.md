## Como Instalar Reportes en los cilentes:
1. Instalar xampp o mamp.
2. Bajar codigo de github del proyecto cognitivobi.
3. Pegar el codigo en la carpeta htdocs de xampp o mamp lo que sea instalado en la maquina del cliente.
4. Hacer uno nuevo archivo “.env” en la carpeta del proyecto cognitvobi en la carpeta htdocs
	htdocs/cognitivobi/.env
5. Copiar los contenidos del archivo .env.example que existe en htdocs/cognitivobi y pegar en el archivo .env.
6. Cambiar cofiguracion de .env según la computadora del cliente.
    DB_HOST=localhost
    DB_DATABASE=raices
    DB_USERNAME=root
    DB_PASSWORD=root
7. Finalmente correr el script cognitivobi.sql para llenar las tablas del reportes.
8. Abrir http://localhost/cognitivobi/public y tendria que ver los reportes instalado

## Como agregar uno nuevo reporte.
1. Escribir el script del nuevo reporte y asegurarle que esta trayendo resultados correctos.
2. Luego llenar los datos en la tabla bi_reportes en el base de datos de cognitivo. Por ejemplo

	en la tabla mencionada arriba:
	id_bi_report : autoincrement primary key.
	Name : Nombre del reporte que va a parecer en la pantalla.
	short_decription: descripcion corta(opcional)
	long_description : descripcion larga (opcional)
	query: el script para traer los datos de reporte.
3. llenar los detalles del reporte en la tabla bi_report_detail en el base de datos de cognitivo. Por ejemplo

	en la tabla mencionada arriba:
	id_bi_report_detail: auto-increment primary key.
	id_bi_report: id del reporte.(foreign key).
	name_column: Nombre de la columna como vas a aparecer en el query del reporte.
	display_column: Nombre de la columna que tiene que aparecer en la pantalla.
	is_output : 1 si esta visible, 0 si esta oculto.
	Group : Si el reporte esta agrupado, esta columna manifesta el nivel del grupo. 1 significa el 	grupo padre, 2 significa hijo del 1.
	order_by: 0 por ahora.
	filter_by: 1 si el reporte se puede filtrar por esta columna, 0 si no.
	aggregate_by : 1 para sumar sin suma corrida. 2 para sumar con suma corrida-
	is_runningotoal ; 0 por ahora.
	is_header: 1si la columna es la cabecera. Null si no.
	is_footer : 1 si la columna es pie de pagina.
	is_drilldown: 0 por ahora.
	chart_axis: 0 por ahora.
	format_column : 0 para texto.1 para numericos. 2 para fecha. 3 para moneda.
