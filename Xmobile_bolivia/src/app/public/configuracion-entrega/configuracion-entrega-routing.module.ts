import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ConfiguracionEntregaPage } from './configuracion-entrega.page';

const routes: Routes = [
  {
    path: '',
    component: ConfiguracionEntregaPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ConfiguracionEntregaPageRoutingModule {}
