import { Injectable } from '@angular/core';
import { Toast } from "@ionic-native/toast/ngx";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { ConfigService } from "../models/config.service";

@Injectable({
    providedIn: 'root'
})
export class NativeService {
    constructor(private toast: Toast, private configService: ConfigService, private spinnerDialog: SpinnerDialog) {
    }

    public mensaje(sms = 'Xmobile', time = '5000', pos = 'bottom') {
        this.toast.show(sms, time, pos).subscribe(toast => {
        });
    }

    public async coordenadas() {
        try {
            return await this.configService.getUbicacion();
        } catch (e) {
            return false;
        }
    }
}
