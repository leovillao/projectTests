  ('01', 'home', 'S', '', 1, 1),

  ('0101', 'Catalogos', 'S', '', 1, 2),

  ('010101', 'Clientes', 'N', 'view-clientes', 1, 0),
  ('010102', 'Etiquetas', 'N', 'view-etiquetas', 1, 0),
  ('010103', 'Subetiquetas', 'N', 'view-setiquetas', 1, 0),
  ( '010104', 'Unidad de Negocios', 'N', 'view-unidad', 1, 0),
  ( '010105', 'Centro de costo', 'N', 'view-costos', 1, 0),
  ( '010106', 'Funciones', 'N', 'view-funciones', 1, 0),
  ( '010107', 'Proveedores', 'N', 'view-proveedor', 1, 0),
  ( '010108', 'Terceros', 'N', 'view-terceros', 1, 0),
  ( '010109', 'Grupos Retenciones', 'N', 'view-setRetenciones', 1, 0),
  ( '010110', 'Bancos', 'N', 'view-bancos', 1, 0),
  ( '010111', 'Tipo Transacciones', 'N', 'view-retenciones', 1, 0),
  ( '010112', 'Cartera', 'N', 'view-cclientes', 1, 0),
  ( '010113', 'Listas de Precios', 'N', 'view-listaprecios', 1, 0),
  ( '010114', 'Empleados', 'N', 'view-ro_viewempleados', 1, 0),


  ('0102', 'Ventas', 'S', '', 1, 3),
  ('010201', 'Ciclo Ventas', 'N', 'view-egresos', 1, 0),
  ('010202', 'Abrir Caja', 'N', 'view-abrirCaja', 1, 0),
  ('010203', 'Cerrar Caja', 'N', 'view-cierreCaja', 1, 0),
  ('010204', 'Aprobacion de pedidos', 'N', 'view-pedidosaprob', 1, 0),
  ('010205', 'Pedidos FDO', 'N', 'view-pedidosFDO', 0, 0),
  ('010206', 'Pedidos CRE', 'N', 'view-pedidosCRE', 1, 0),
  ('010207', 'Facturación', 'N', 'view-facpos', 1, 0),
  ( '010208', 'Facturacion Franquiciado', 'N', 'view-facfdo', 0, 0),
  ( '010209', 'Facturacion Credito', 'N', 'view-factcred', 1, 0),
  ( '010210', 'Devolucion en venta', 'N', 'view-ve_devolucion', 0, 0),
  ( '010211', 'Documentos FDO', 'N', 'view-documentosFDO', 1, 0),
  ( '010212', 'Reenvio Documentos', 'N', 'view-ve_docAutorizar', 1, 0),
  ( '010213', 'Ventas', 'N', 'view-in_informeVentas', 1, 0),

  ( '0103', 'Compras', 'S', '', 1, 3),

  ( '010301', 'CM- Compras', 'N', 'view-ingresos', 1, 0),
  ( '010302', 'Credito', 'N', 'view-cartera', 1, 0),

  ( '0104', 'Tesoreria', 'S', '', 1, 3),

  ( '010406', 'Anticipos', 'N', 'view-listanticipos', 1, 0),
  ( '010405', 'Cobros Vendedores', 'N', 'view-paymenths', 1, 0),
  ( '010402', 'Cobros', 'N', 'view-cobros', 1, 0),
  ( '010403', 'Anticipos', 'N', 'view-reportAnticipos', 1, 0),


  ( '0105', 'Reportes', 'S', '', 1, 5),
  ( '010501', 'Reporte', 'N', 'view-reports', 1, 0),


  ( '0106', 'Inventario', 'S', '', 1, 4),
  ( '010601', 'Productos', 'N', 'view-products', 1, 3),
  ( '010602', 'Ingresos', 'N', 'view-in_ingresos', 1, 1),
  ( '010603', 'Egresos', 'N', 'view-in_egresos', 1, 2),
  ( '010604', 'Transferencia', 'N', 'view-in_transferencia', 1, 0),
  ( '010605', 'Toma Fisica Inventario', 'N', 'view-tf_eventos', 1, 5);
  ( '010606', 'kardex', 'N', 'view-in_kardex', 1, 4),
  ( '010607', 'Saldos', 'N', 'view-in_informesSaldos', 1, 0),


  ( '0110', 'Configuraciones', 'S', '', 1, 6),
  ( '011001', 'Accesos', 'N', 'view-accesos', 1, 0),
  ( '011002', 'Perfiles', 'N', 'view-listperfiles', 1, 0),
  ( '011003', 'Usuarios', 'N', 'view-users', 1, 0),
  ( '011004', 'Puntos de emision', 'N', 'view-secuencias', 1, 0),