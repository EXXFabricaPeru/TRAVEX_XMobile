import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from "@angular/router";
import {Documentos} from "../../models/documentos";
import {ModalController, NavController} from "@ionic/angular";
import {ModalpagosPage} from "../modalpagos/modalpagos.page";
import {Clientes} from "../../models/clientes";

@Component({
    selector: 'app-pendientes',
    templateUrl: './pendientes.page.html',
    styleUrls: ['./pendientes.page.scss'],
})
export class PendientesPage implements OnInit {

    public id: any;
    public ventas: any;
    public clienteName: any;
    public totalx: any;

    constructor(private activatedRoute: ActivatedRoute, private navCrl: NavController,
                public modalController: ModalController) {
        this.id = this.activatedRoute.snapshot.paramMap.get('id');
        this.totalx = [];
    }

    public async ngOnInit() {
        let cliente = new Clientes();
        let datax: any = await cliente.selectCarCode(this.id);
        this.clienteName = datax.CardName;
        this.listarPedidos();
    }

    public async listarPedidos() {
        let pedidos = new Documentos();
        this.ventas = await pedidos.findAllPedidos(this.id);
        this.totalx = await pedidos.findAllTotal(this.id);
    }

    public async pagos(item: any) {
        let obj: any = {
            component: ModalpagosPage,
            componentProps: item
        };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then((respuesta: any) => {
            this.listarPedidos();
        });
        return await modal.present();
    }

    public convierteFecha(tiempo: any): string {
        let documentos = new Documentos();
        return documentos.getFechaTime(tiempo);
    }
}
