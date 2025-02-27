import {Component, OnInit} from '@angular/core';
import {Network} from "@ionic-native/network/ngx";
import {Toast} from "@ionic-native/toast/ngx";
import {ConfigService} from "../../models/config.service";
import * as moment from 'moment';

@Component({
    selector: 'app-net',
    templateUrl: './net.component.html',
    styleUrls: ['./net.component.scss'],
})
export class NetComponent implements OnInit {
    public conexion: string;
    public xcc: boolean;
    public tc: any;

    constructor(private network: Network, private configService: ConfigService,
                private toast: Toast) {
        this.conexion = '';
        this.xcc = false;
        this.tc = '';
    }

    public async ngOnInit() {
        this.conexionVerifica();
        setTimeout(() => {
            this.tipocambio();
        }, 500);
    }

    private async tipocambio() {
        try {
            let respUs: any = await this.configService.getSession();
            let cambioparalelo: any = respUs[0].tipocambioparalelo;
            if (cambioparalelo.fecha == moment().format('YYYY-MM-DD')) {
                this.tc = cambioparalelo.tipoCambio;
            } else {
                this.tc = 0;
            }
        } catch (e) {
            console.log(e);
            this.tc = 0;
        }
    }

    public conexionVerifica() {
        this.network.onDisconnect().subscribe(() => {
            this.conexion = 'medium';
            this.configService.setModo('none');
            this.toast.show(`MODO OFFLINE`, '3000', 'top').subscribe(toast => {
            });
        });
        this.network.onConnect().subscribe(() => {
        });
    }


    public async sqlite() {

    }
}
