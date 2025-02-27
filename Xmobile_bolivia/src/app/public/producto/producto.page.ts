import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ConfigService } from "../../models/config.service";
import { Productos } from "../../models/productos";
import { Productosprecios } from "../../models/productosprecios";
import { File } from '@ionic-native/file/ngx';
import { WebView } from "@ionic-native/ionic-webview/ngx";
import { Lotesproductos } from "../../models/lotesproductos";
import { Productosalmacenes } from '../../models/productosalmacenes';

@Component({
    selector: 'app-producto',
    templateUrl: './producto.page.html',
    styleUrls: ['./producto.page.scss'],
})
export class ProductoPage implements OnInit {
    public id: any;
    public idUser: any;
    public data: any;
    public items: any;
    public lotes: any;
    public almacenes: any;
    public srcimg: any;

    constructor(private activatedRoute: ActivatedRoute, private configService: ConfigService,
        private file: File, private webview: WebView) {
        this.data = [];
        this.items = [];
        this.lotes = [];
    }

    async ngOnInit() {
        this.id = this.activatedRoute.snapshot.paramMap.get('id');
        let id: any = await this.configService.getSession();
        this.idUser = id.idUsuario;
        let productos = new Productos();
        let modelAlmacenes = new Productosalmacenes();

        this.data = await productos.select(this.id);
        console.log("stock ",);
        this.almacenes = await modelAlmacenes.findOne(this.data.ItemCode);
        let costoTotalStock = this.almacenes.reduce(function (totalActual, value) {
            return value.InStock + totalActual;
        }, 0); // El 0 será la cantidad inicial con la que comenzará el totalActual

        let costoTotalComprometido = this.almacenes.reduce(function (totalActual, value) {
            return value.Committed + totalActual;
        }, 0); // El 0 será la cantidad inicial con la que comenzará el totalActual

        this.data.QuantityOnStock = Number(costoTotalStock).toFixed(0);
        this.data.QuantityOrderedFromVendors = Number(costoTotalComprometido).toFixed(0);
        console.log(" this.costoTotalStock ", costoTotalStock);

        console.log(" this.costoTotalComprometido ", costoTotalComprometido);

        console.log(" this.data ", this.data);
        console.log("this.almacenes  ", this.almacenes);
        // console.log(this.data);


        let pathExternal = this.file.externalApplicationStorageDirectory;
        let imgp: any = this.data.ItemCode + ".jpg";
        this.file.checkFile(pathExternal, imgp).then((data) => {
            this.srcimg = this.webview.convertFileSrc(pathExternal + imgp);
        }).catch((err) => {
            this.srcimg = '../../../assets/broken-image.svg';
        });
        this.listar();
    }

    public async listar() {
        let precios = new Productosprecios();
      
        this.items = await precios.selectPrecios(this.data.ItemCode);
       
        this.lotesarr();
    }

    public async lotesarr() {
        this.lotes = [];
        let lotes = new Lotesproductos();
        this.lotes = await lotes.lotesproductos(this.data.ItemCode);
    }
}