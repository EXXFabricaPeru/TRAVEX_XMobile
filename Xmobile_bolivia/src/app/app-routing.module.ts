import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';

const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'home', loadChildren: './home/home.module#HomePageModule' },
    { path: 'list', loadChildren: './list/list.module#ListPageModule' },
    { path: 'login', loadChildren: './public/login/login.module#LoginPageModule' },
    { path: 'register', loadChildren: './public/register/register.module#RegisterPageModule' },
    { path: 'config', loadChildren: './public/config/config.module#ConfigPageModule' },
    { path: 'sincronizacion', loadChildren: './public/sincronizacion/sincronizacion.module#SincronizacionPageModule' },
    { path: 'productos', loadChildren: './public/productos/productos.module#ProductosPageModule' },
    { path: 'clientes/:id', loadChildren: './public/clientes/clientes.module#ClientesPageModule' },
    { path: 'producto/:id', loadChildren: './public/producto/producto.module#ProductoPageModule' },
    { path: 'cliente/:id', loadChildren: './public/cliente/cliente.module#ClientePageModule' },
    { path: 'pedido/:id/:tp/:cli', loadChildren: './public/pedido/pedido.module#PedidoPageModule' },
    { path: 'modalcliente', loadChildren: './public/modalcliente/modalcliente.module#ModalclientePageModule' },
    { path: 'modalproducto', loadChildren: './public/modalproducto/modalproducto.module#ModalproductoPageModule' },
    { path: 'detalleventa', loadChildren: './public/detalleventa/detalleventa.module#DetalleventaPageModule' },
    { path: 'popover', loadChildren: './public/popover/popover.module#PopoverPageModule' },
    { path: 'pedidos/:id', loadChildren: './pedidos/pedidos.module#PedidosPageModule' },
    { path: 'ruta', loadChildren: './ruta/ruta.module#RutaPageModule' },
    { path: 'pagos', loadChildren: './public/pagos/pagos.module#PagosPageModule' },
    { path: 'pendientes/:id', loadChildren: './public/pendientes/pendientes.module#PendientesPageModule' },
    { path: 'modalpagos', loadChildren: './public/modalpagos/modalpagos.module#ModalpagosPageModule' },
    { path: 'formcliente/:id', loadChildren: './public/formcliente/formcliente.module#FormclientePageModule' },
    { path: 'anular', loadChildren: './public/anular/anular.module#AnularPageModule' },
    { path: 'perfil', loadChildren: './public/perfil/perfil.module#PerfilPageModule' },
    { path: 'agendas', loadChildren: './public/agendas/agendas.module#AgendasPageModule' },
    {
        path: 'formagenda/:CardCode/:CardName/:id',
        loadChildren: './public/formagenda/formagenda.module#FormagendaPageModule'
    },
    { path: 'agenda/:id', loadChildren: './public/agenda/agenda.module#AgendaPageModule' },
    { path: 'mapacliente/:id/:mode', loadChildren: './mapacliente/mapacliente.module#MapaclientePageModule' },
    { path: 'detallepago/:id', loadChildren: './public/detallepago/detallepago.module#DetallepagoPageModule' },
    { path: 'detallepedido/:id', loadChildren: './public/detallepedido/detallepedido.module#DetallepedidoPageModule' },
    { path: 'informes', loadChildren: './public/informes/informes.module#InformesPageModule' },
    { path: 'modalseries', loadChildren: './public/modalseries/modalseries.module#ModalseriesPageModule' },
    { path: 'frompagos', loadChildren: './public/frompagos/frompagos.module#FrompagosPageModule' },
    { path: 'visitas', loadChildren: './public/visitas/visitas.module#VisitasPageModule' },
  {
    path: 'modal-nit',
    loadChildren: () => import('./public/modal-nit/modal-nit.module').then( m => m.ModalNitPageModule)
  },
  {
    path: 'modal-modopago',
    loadChildren: () => import('./public/modal-modopago/modal-modopago.module').then( m => m.ModalModopagoPageModule)
  },
  {
    path: 'configuracion-entrega',
    loadChildren: () => import('./public/configuracion-entrega/configuracion-entrega.module').then( m => m.ConfiguracionEntregaPageModule)
  }, 

];

@NgModule({
    imports: [
        RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
    ],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
