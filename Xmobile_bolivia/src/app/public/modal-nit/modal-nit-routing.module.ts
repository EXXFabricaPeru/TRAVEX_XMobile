import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ModalNitPage } from './modal-nit.page';

const routes: Routes = [
  {
    path: '',
    component: ModalNitPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ModalNitPageRoutingModule {}
