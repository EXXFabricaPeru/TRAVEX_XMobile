import { ComponentsModule } from 'src/app/components/components.module';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule } from '@angular/forms';
import { FirmaPage } from './firma.page';

@NgModule({
  imports: [
    ComponentsModule,
    FormsModule,
    IonicModule,
    CommonModule,
  ],
  declarations: [FirmaPage]
})
export class FirmaPageModule { }
