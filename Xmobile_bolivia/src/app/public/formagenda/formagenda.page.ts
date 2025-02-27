import {Component, OnInit} from '@angular/core';
import {Dialogs} from "@ionic-native/dialogs/ngx";
import {Agendas} from "../../models/agendas";
import {ConfigService} from "../../models/config.service";
import {Toast} from "@ionic-native/toast/ngx";
import {File} from '@ionic-native/file/ngx';
import {NavController} from "@ionic/angular";
import {DataService} from "../../services/data.service";
import {WebView} from '@ionic-native/ionic-webview/ngx';
import {Network} from '@ionic-native/network/ngx';
import {SpinnerDialog} from '@ionic-native/spinner-dialog/ngx';
import {WheelSelector} from '@ionic-native/wheel-selector/ngx';
import {HTTP} from '@ionic-native/http/ngx';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'app-formagenda',
    templateUrl: './formagenda.page.html',
    styleUrls: ['./formagenda.page.scss'],
})
export class FormagendaPage implements OnInit {
    public id: number;
    public tipoActividad: string;
    public estado: string;
    public Cardcode: string;
    public nombreCliente: string;
    public PhoneNumber: number;
    public fecha: string;
    public hora: string;
    public asunto: string;
    public comentarios: string;
    public dateUpdate: string;
    public textAction: string;
    public tipoActividadDescripcion: string;
    public EstadoDescripcion: string;
    public AsuntoDescripcion: string;
    public minPiker: string;
    public data: any;

    constructor(private activatedRoute: ActivatedRoute, private spinnerDialog: SpinnerDialog, private http: HTTP,
                private file: File, private dialogs: Dialogs, private network: Network,
                private toast: Toast, private webview: WebView, private configService: ConfigService, private selector: WheelSelector,
                private navCrl: NavController, private dataService: DataService) {
        this.textAction = 'Registrar actividad';
        let agenda = new Agendas();
        this.fecha = agenda.getFechaPicker();
        this.hora = agenda.getHoraSinSegundosCurrent();
        this.minPiker = agenda.getFechaPicker();
        this.data = [];
    }

    async ngOnInit() {
        this.Cardcode = this.activatedRoute.snapshot.paramMap.get("CardCode");
        this.nombreCliente = this.activatedRoute.snapshot.paramMap.get("CardName");
        this.id = this.activatedRoute.snapshot.paramMap.get("id") == 'NULL' ? -1 : parseInt(this.activatedRoute.snapshot.paramMap.get("id"));
        if (this.id != -1) {
            let agenda = new Agendas();
            this.data = await agenda.findById(this.id);
            this.id = undefined;
            this.tipoActividadDescripcion = this.data.actividad;
            this.EstadoDescripcion = this.data.estado;
            this.AsuntoDescripcion = this.data.asunto;
            this.fecha = this.data.fecha;
            this.hora = this.data.hora;
            this.comentarios = this.data.comentarios;
            this.PhoneNumber = this.data.PhoneNumber;
            this.tipoActividad = this.data.idActividad;
            this.estado = this.data.idEstado;
            this.asunto = this.data.idAsunto;
        }
    }

    public async register() {
        let agendas = new Agendas();
        this.dateUpdate = agendas.getFechaPicker();
        let datauser: any = await this.configService.getSession();
        let data = {
            Id: this.id,
            idUser: datauser.idUsuario,
            CardCode: this.Cardcode,
            Actividad: this.tipoActividad,
            Estado: this.estado,
            Asunto: this.asunto,
            Fecha: this.fecha,
            Hora: this.hora,
            Telefono: this.PhoneNumber,
            Comentarios: this.comentarios,
            DateUpdate: this.dateUpdate
        };
        this.registreData(data, datauser);
    }

    public async editData(data: any, datauser: any) {

    }

    public async registreData(data: any, datauser: any) {
        let agenda = new Agendas();
        let err: any = 'OK';
        this.spinnerDialog.show(null, 'Buscando espere...', true);
        if (err == 'OK') {
            if (this.network.type != 'none') {
                try {
                    let arr = [];
                    await agenda.insertRegister(data, datauser.idUsuario);
                    this.toast.show('Se registró correctamente.', '3000', 'top').subscribe(toast => {
                    });
                    this.spinnerDialog.hide();
                    this.navCrl.navigateForward(`agendas`);
                } catch (e) {
                    this.spinnerDialog.hide();
                    this.toast.show('Error al registrar.', '3000', 'top').subscribe(toast => {
                    });
                    this.navCrl.pop();
                }
            } else {
                try {
                    await agenda.insertRegister(data, datauser.idUsuario);
                    this.toast.show('Se registró correctamente.', '3000', 'top').subscribe(toast => {
                    });
                    this.spinnerDialog.hide();
                    this.navCrl.pop();
                } catch (e) {
                    this.spinnerDialog.hide();
                    this.navCrl.pop();
                }
            }
        } else {
            this.spinnerDialog.hide();
            this.toast.show(err, '3000', 'top').subscribe(toast => {
            });
        }
    }

    public async selectTipoActividad() {
        let agenda = new Agendas();
        let terr: any = await agenda.selectTipoActividades();
        let listactividades = [];
        for (let t of terr)
            listactividades.push({description: t.descripcion, id: t.id});
        if (listactividades.length > 0) {
            this.selector.show({
                title: "Selecionar tipo de actividad.",
                items: [listactividades],
                positiveButtonText: "Seleccionar",
                negativeButtonText: "Cancelar"
            }).then((result: any) => {
                let resp: any = listactividades[result[0].index];
                this.tipoActividad = resp.id;
                this.tipoActividadDescripcion = resp.description;
            }, (err: any) => {
                console.log(err)
            });
        }
    }

    public async selectEstado() {
        let agenda = new Agendas();
        let terr: any = await agenda.selectEstadoActividades();
        let listaestados = [];
        for (let t of terr)
            listaestados.push({description: t.descripcion, id: t.id});
        if (listaestados.length > 0) {
            this.selector.show({
                title: "SELECCIONAR ESTADO.",
                items: [listaestados],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then((result: any) => {
                let resp: any = listaestados[result[0].index];
                this.estado = resp.id;
                this.EstadoDescripcion = resp.description;
            }, (err: any) => {
                console.log(err)
            });
        }
    }

    public async selectAsunto() {
        let agenda = new Agendas();
        let terr: any = await agenda.selectAsunto();
        let listaasuntos = [];
        for (let t of terr)
            listaasuntos.push({description: t.descripcion, id: t.id});
        if (listaasuntos.length > 0) {
            this.selector.show({
                title: "SELECCIONAR ASUNTO.",
                items: [listaasuntos],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then((result: any) => {
                let resp: any = listaasuntos[result[0].index];
                this.asunto = resp.id;
                this.AsuntoDescripcion = resp.description;
            }, (err: any) => {
                console.log(err)
            });
        }
    }
}
