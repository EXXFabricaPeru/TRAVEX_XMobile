import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ModalModopagoPageRoutingModule } from './modal-modopago-routing.module';

import { ModalModopagoPage } from './modal-modopago.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ModalModopagoPageRoutingModule
  ],
  declarations: [ModalModopagoPage]
})
export class ModalModopagoPageModule {}
