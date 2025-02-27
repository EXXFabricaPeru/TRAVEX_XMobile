import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { FromevidenciaPage } from './fromevidencia.page';

const routes: Routes = [
  {
    path: '',
    component: FromevidenciaPage
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RouterModule.forChild(routes)
  ],
  declarations: [FromevidenciaPage]
})
export class FFromevidenciaPageModule {}
