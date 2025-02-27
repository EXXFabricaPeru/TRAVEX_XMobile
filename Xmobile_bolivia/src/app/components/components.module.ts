import {CUSTOM_ELEMENTS_SCHEMA, NgModule} from '@angular/core';
import {NetComponent} from "./net/net.component";

@NgModule({
    declarations: [NetComponent],
    exports: [NetComponent],
    schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class ComponentsModule {

}