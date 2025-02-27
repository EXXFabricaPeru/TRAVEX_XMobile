import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser'
import { IonicModule } from '@ionic/angular';
import { Routes, RouterModule } from '@angular/router';

import { ModalMapaPage } from './modalmapa.page';
const routes: Routes = [
  {
    path: '',
    component: ModalMapaPage
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
  declarations: [ModalMapaPage]
})
export class ModalMapaPageModule { }
