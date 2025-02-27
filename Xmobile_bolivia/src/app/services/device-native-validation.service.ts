import { Injectable } from '@angular/core';
import { BackgroundGeolocation } from '@ionic-native/background-geolocation/ngx';
@Injectable({
    providedIn: 'root'
})
export class DeviceNativeValidationService {

    constructor(
        private backgroundGeolocation: BackgroundGeolocation
    ) { }
    /**
     * return true or fale si el gps esta encendido
     */
    async isGpsLocationEnabled() {
        return this.backgroundGeolocation.isLocationEnabled();
    }
    switchToLocationSettings() {
        return this.backgroundGeolocation.showLocationSettings();

    }
}
