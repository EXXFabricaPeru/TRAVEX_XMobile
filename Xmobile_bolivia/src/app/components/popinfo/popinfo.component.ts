import { Component, OnInit } from '@angular/core';
import { PopoverController,NavParams } from '@ionic/angular';
import { ConfigService } from "../../models/config.service";

@Component({
    selector: 'app-popinfo',
    templateUrl: './popinfo.component.html',
    styleUrls: ['./popinfo.component.scss'],
})
export class PopinfoComponent implements OnInit {
    public menus: any;

    constructor(public popoverController: PopoverController, private configService: ConfigService,public navParams: NavParams) {
    }

    public async ngOnInit() {
        let item = this.navParams.data;
        console.log("stockBoni ",item.estado);
        let opciones: any = [];
        //console.log("this.configService",this.configService.getTipo());
        //let tp = await this.configService.getTipo();
        if(item.estado == 3){
            switch (item.tipo) {
                case ('DOF')://OFERTAS
                    opciones = [{
                        titulo: 'Duplicar',
                        icon: 'albums',
                        tipo: 'DOFX',
                    }];
                    break;
                case ('DOP')://PEDIDOS
                    opciones = [{
                        titulo: 'Duplicar',
                        icon: 'albums',
                        tipo: 'DOPX',
                    }];
                    break;
            }
        }else{
            switch (item.tipo) {
                case ('DFA'): //FACTURAS
                    opciones = [
                        /*{
                            titulo: 'Pagar',
                            icon: 'logo-usd',
                            tipo: 2,
                        },*/{
                            titulo: 'Anular',
                            icon: 'warning',
                            tipo: 3,
                        }, {
                            titulo: 'Estado',
                            icon: 'warning',
                            tipo: 4,
                        },{
                            titulo: 'Entregar',
                            icon: 'arrow-redo',
                            tipo: 'DOE',
                        }];
                    break;
                case ('DOE')://ENTREGAS
                    opciones = [{
                        titulo: 'Cop. factura',
                        icon: 'copy',
                        tipo: 'DFA',
                    }, {
                        titulo: 'Evidencia',
                        icon: 'camera',
                        tipo: 'FED',
                    }];
                    break;
                case ('DOF')://OFERTAS
                    opciones = [{
                        titulo: 'Pagar',
                        icon: 'logo-usd',
                        tipo: 2,
                    },/*{
                        titulo: 'Pagar USD',
                        icon: 'logo-usd',
                        tipo: 1,
                    }, */{
                        titulo: 'Anular',
                        icon: 'warning',
                        tipo: 3,
                    }, {
                        titulo: 'Duplicar',
                        icon: 'albums',
                        tipo: 'DOFX',
                    }, {
                        titulo: 'Cop. pedido',
                        icon: 'copy',
                        tipo: 'DOP',
                    }];
                    break;
                case ('DOP')://PEDIDOS
                    opciones = [{
                        titulo: 'Pagar',
                        icon: 'logo-usd',
                        tipo: 2,
                    },
                    {
                        titulo: 'Anular',
                        icon: 'warning',
                        tipo: 3,
                    },{
                        titulo: 'Estado',
                        icon: 'warning',
                        tipo: 4,
                    },{
                        titulo: 'Duplicar',
                        icon: 'albums',
                        tipo: 'DOPX',
                    }, {
                        titulo: 'Cop. factura',
                        icon: 'copy',
                        tipo: 'DFA',
                    }, {
                        titulo: 'Entregar',
                        icon: 'arrow-redo',
                        tipo: 'DOE',
                    }, {
                        titulo: 'Evidencia',
                        icon: 'camera',
                        tipo: 'FED',
                    }];
                    break;
            }
        }
        
        this.menus = opciones;
    }

    public accion(index: any) {
        this.popoverController.dismiss({
            index: index
        })
    }
}
