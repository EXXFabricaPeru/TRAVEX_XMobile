import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { DetalleventaPage } from './detalleventa.page';
import { PipesModule } from 'src/app/pipes/pipe.module';

const routes: Routes = [
  {
    path: '',
    component: DetalleventaPage
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RouterModule.forChild(routes),
    PipesModule
  ],
  declarations: [DetalleventaPage]
})
export class DetalleventaPageModule {}
