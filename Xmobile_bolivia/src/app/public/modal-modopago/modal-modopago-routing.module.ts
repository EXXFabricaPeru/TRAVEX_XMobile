import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ModalModopagoPage } from './modal-modopago.page';

const routes: Routes = [
  {
    path: '',
    component: ModalModopagoPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ModalModopagoPageRoutingModule {}
