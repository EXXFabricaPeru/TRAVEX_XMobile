import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ConfiguracionEntregaPageRoutingModule } from './configuracion-entrega-routing.module';

import { ConfiguracionEntregaPage } from './configuracion-entrega.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ConfiguracionEntregaPageRoutingModule
  ],
  declarations: [ConfiguracionEntregaPage]
})
export class ConfiguracionEntregaPageModule {}
