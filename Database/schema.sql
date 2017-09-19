create table vehiculo (
	id integer not null auto_increment,
	modelo varchar(128),
	agencia varchar(128),
	exterior varchar(64),
	interior varchar(64),
	equipo varchar(128),
	fentrega date,
	precio double,
	estado boolean,
	primary key(id)
);
create table descuento (
	id integer not null auto_increment,
	vehiculo_id integer not null,
	tipo varchar(16),
	cantidad double,
	primary key(id),
	foreign key(vehiculo_id)
	references vehiculo(id)
	on delete cascade
);
-- fecha aprox el mismo que vehiculo
create table reserva(
	id integer not null auto_increment,
	vehiculo_id integer not null,
	comprador varchar(128),
	vendedor varchar(128),
	primary key(id),
	foreign key(vehiculo_id)
	references vehiculo(id)
	on delete cascade
);
create table apartados (
	id integer not null auto_increment,
	reserva_id integer not null,
	cantidad double,
	urlImgComprobate varchar(128),
	fecha date,
	primary key(id),
	foreign key(reserva_id)
	references reserva(id)
	on delete cascade
);
create table facturacion(
	id integer not null auto_increment,
	reserva_id integer not null,
	vin varchar(128),
	ffact date,
	primary key(id),
	foreign key(reserva_id)
	references reserva(id)
	on delete cascade
);
create table comision(
	id integer not null auto_increment,
	facturacion_id integer not null,
	tipo varchar(16),
	cantidad double,
	primary key(id),
	foreign key(facturacion_id)
	references facturacion(id)
	on delete cascade
);
create table pago(
	id integer not null auto_increment,
	facturacion_id integer not null,
	monto double,
	fecha date,
	primary key(id),
	foreign key(facturacion_id)
	references facturacion(id)
	on delete cascade
);
create table factura(
	id integer not null auto_increment,
	facturacion_id integer not null,
	urlPdf varchar(128),
	urlXml varchar(128),
	primary key(id),
	foreign key(facturacion_id)
	references facturacion(id)
	on delete cascade
);
-- puede que factura dependa de venta
-- fentrega FECHA DE ENTREGA FISICA
-- AGREGAR FECHA TRASPASO DONDE EL VEHICULO SE
-- PASAR DE LA LISTA DE FACTURACION A LA LISTA DE FACTURAR.
-- EDITAR ENTREGA, LUEGO ENTREGA FINAL.
create table entrega(
	id integer not null auto_increment,
	facturacion_id integer not null,
	entrega date,
	entregaFinal date,
	factura boolean,
	cargador boolean,
	notas text,
	fproduccion date null,
	primary key(id),
	foreign key(facturacion_id)
	references facturacion(id)
	on delete cascade
);
