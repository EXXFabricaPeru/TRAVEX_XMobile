import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule} from '@angular/forms';
import {Routes, RouterModule} from '@angular/router';

import {IonicModule} from '@ionic/angular';

import {InformesPage} from './informes.page';
import {ComponentsModule} from "../../components/components.module";

const routes: Routes = [
    {
        path: '',
        component: InformesPage
    }
];

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        IonicModule,
        ComponentsModule,
        RouterModule.forChild(routes)
    ],
    declarations: [InformesPage]
})
export class InformesPageModule {
}
