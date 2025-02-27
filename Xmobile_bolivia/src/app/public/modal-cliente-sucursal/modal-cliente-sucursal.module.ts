import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser'
import { IonicModule } from '@ionic/angular';
import { Routes, RouterModule } from '@angular/router';

import { ModalClienteSucursalPage } from './modal-cliente-sucursal.page';
const routes: Routes = [
  {
    path: '',
    component: ModalClienteSucursalPage
  }
];

@NgModule({
  imports: [BrowserModule,
    CommonModule,
    FormsModule,
    IonicModule,
    ReactiveFormsModule,
    RouterModule.forChild(routes)
  ],
  declarations: [ModalClienteSucursalPage]
})
export class ModalClienteSucursalPageModule { }
