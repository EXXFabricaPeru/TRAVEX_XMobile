import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { RouteReuseStrategy } from '@angular/router';

import { IonicModule, IonicRouteStrategy } from '@ionic/angular';
import { SplashScreen } from '@ionic-native/splash-screen/ngx';
import { StatusBar } from '@ionic-native/status-bar/ngx';

import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';

import { BackgroundGeolocation } from '@ionic-native/background-geolocation/ngx';
import { AndroidFingerprintAuth } from '@ionic-native/android-fingerprint-auth/ngx';
import { HTTP } from '@ionic-native/http/ngx';
import { Camera } from '@ionic-native/camera/ngx';
import { Device } from '@ionic-native/device/ngx';
import { Dialogs } from '@ionic-native/dialogs/ngx';
import { UniqueDeviceID } from '@ionic-native/unique-device-id/ngx';
import { File } from '@ionic-native/file/ngx';
import { FileOpener } from '@ionic-native/file-opener/ngx';
import { FileTransfer } from '@ionic-native/file-transfer/ngx';
import { FilePath } from '@ionic-native/file-path/ngx';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { NativeStorage } from '@ionic-native/native-storage/ngx';
import { Network } from '@ionic-native/network/ngx';
import { LocationAccuracy } from '@ionic-native/location-accuracy/ngx';
import { Vibration } from '@ionic-native/vibration/ngx';
import { Toast } from '@ionic-native/toast/ngx';
import { SQLite } from '@ionic-native/sqlite/ngx';
import { WheelSelector } from '@ionic-native/wheel-selector/ngx';
import { BarcodeScanner } from '@ionic-native/barcode-scanner/ngx';
import { WebView } from '@ionic-native/ionic-webview/ngx';
import { FrompagosPageModule } from "./public/frompagos/frompagos.module";
import { HttpClientModule } from "@angular/common/http";
import { ModalseriesPageModule } from "./public/modalseries/modalseries.module";
import { ModalpagosPageModule } from "./public/modalpagos/modalpagos.module";
import { ModalproductoPageModule } from "./public/modalproducto/modalproducto.module";
import { ModalclientePageModule } from "./public/modalcliente/modalcliente.module";
import { DetalleventaPageModule } from "./public/detalleventa/detalleventa.module";
import { AnularPageModule } from "./public/anular/anular.module";
import { PopoverPageModule } from "./public/popover/popover.module";
import { VisitasPageModule } from "./public/visitas/visitas.module";
import { PopinfoComponent } from "./components/popinfo/popinfo.component";
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { NativeGeocoder } from '@ionic-native/native-geocoder/ngx';
import { AndroidPermissions } from '@ionic-native/android-permissions/ngx';
import { Diagnostic } from '@ionic-native/diagnostic/ngx';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { FormclientePageModule } from './public/formcliente/formcliente.module';
import { DetallepagoPageModule } from './public/detallepago/detallepago.module';
import { SQLitePorter } from '@ionic-native/sqlite-porter/ngx';
import { InAppBrowser } from '@ionic-native/in-app-browser/ngx';

@NgModule({
    declarations: [AppComponent, PopinfoComponent],
    entryComponents: [],
    imports: [
        BrowserModule,
        IonicModule.forRoot(),
        AppRoutingModule,
        FrompagosPageModule,
        ModalclientePageModule,
        ModalproductoPageModule,
        ModalpagosPageModule,
        ModalseriesPageModule,
        DetalleventaPageModule,
        DetallepagoPageModule,
        AnularPageModule,
        PopoverPageModule,
        VisitasPageModule,
        HttpClientModule,
        FormclientePageModule


    ],
    providers: [
        StatusBar,
        SplashScreen,
        BackgroundGeolocation,
        AndroidFingerprintAuth,
        HTTP,
        Camera,
        Device,
        Dialogs,
        UniqueDeviceID,
        File,
        FileOpener,
        FileTransfer,
        FilePath,
        SpinnerDialog,
        NativeStorage,
        Network,
        LocationAccuracy,
        Vibration,
        Toast,
        SQLite,
        WheelSelector,
        BarcodeScanner,
        WebView,
        Geolocation,
        NativeGeocoder,
        AndroidPermissions,
        Diagnostic,
        SQLitePorter,
	    InAppBrowser, 
        { provide: RouteReuseStrategy, useClass: IonicRouteStrategy }
        
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
