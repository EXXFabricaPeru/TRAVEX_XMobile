import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ModalNitPageRoutingModule } from './modal-nit-routing.module';

import { ModalNitPage } from './modal-nit.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ModalNitPageRoutingModule
  ],
  declarations: [ModalNitPage]
})
export class ModalNitPageModule {}
