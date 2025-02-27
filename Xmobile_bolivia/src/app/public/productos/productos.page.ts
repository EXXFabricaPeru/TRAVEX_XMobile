import { Component, OnInit } from '@angular/core';
import { ConfigService } from "../../models/config.service";
import { Productos } from "../../models/productos";
import { NavController } from "@ionic/angular";
import { Network } from "@ionic-native/network/ngx";
import { Toast } from "@ionic-native/toast/ngx";

@Component({
    selector: 'app-productos',
    templateUrl: './productos.page.html',
    styleUrls: ['./productos.page.scss'],
})
export class ProductosPage implements OnInit {
    public id: number;
    public items: any;
    public textLoad: any;
    public loadItem: boolean;
    public conexion: boolean;
    public xcc: boolean;
    public lmt: number;
    public search: string;

    constructor(private configService: ConfigService, private toast: Toast,
        private network: Network, private navCrl: NavController) {
        this.items = [];
        this.loadItem = false;
        this.conexion = false;
        this.xcc = true;
        this.lmt = 0;
        this.search = '';
        this.textLoad = '';
    }

    public async ngOnInit() {
        let id: any = await this.configService.getSession();
        this.id = id.idUsuario;
        this.dataProductos();
    }

    public async dataProductos() {
        try {
            let productos = new Productos();
            let datax: any = await productos.findAll(this.lmt, this.search);
            console.log("datax ", datax);
            for (let itm of datax) {
                this.items.push(itm)
            }
            (this.items.length > 0) ? this.textLoad = '' : this.textLoad = 'Sin resultados';
        } catch (e) {
            console.log(e);
        }
    }

    public loadData(event) {
        // this.search = "";
        setTimeout(() => {
            this.lmt += 20;
            this.dataProductos();
            event.target.complete();
        }, 500);
    }

    public async buscar(event: any) {
        this.items = [];
        let search = event.detail.value;
        this.search = search;
        this.lmt = 0;
        let productos = new Productos();
        let datax: any = await productos.findAll(this.lmt, this.search);
        console.log("datax ", datax);
        for (let itm of datax) {
            this.items.push(itm)
        }
    }

    public findItem(item: any) {
        this.navCrl.navigateForward(`producto/` + item.ItemCode);
    }


    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }
}
