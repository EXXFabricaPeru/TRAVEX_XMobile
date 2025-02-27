import {Component, OnInit, ViewChild} from '@angular/core';
import {ConfigService} from "../../models/config.service";
import {Agendas} from "../../models/agendas";
import {NavController} from "@ionic/angular";
import {Toast} from "@ionic-native/toast/ngx";
import {ActivatedRoute} from '@angular/router';
import {WheelSelector} from '@ionic-native/wheel-selector/ngx';

@Component({
    selector: 'app-agenda',
    templateUrl: './agenda.page.html',
    styleUrls: ['./agenda.page.scss'],
})
export class AgendaPage implements OnInit {
    public id: string;
    public data: any;
    public estadoId: any;
    public textAction: string;
    public EstadoDescripcion: string;
    public asuntoId: any;
    public AsuntoDescripcion: string;
    public comentarios: string;

    constructor(private configService: ConfigService, private navCrl: NavController, private toast: Toast,
                private activatedRoute: ActivatedRoute, private selector: WheelSelector) {
        this.data = [];
        this.textAction = 'Actualizar estado';
    }

    public async ngOnInit() {
        this.id = this.activatedRoute.snapshot.paramMap.get("id");
        let agenda = new Agendas();
        this.data = await agenda.findById(this.id);
        this.EstadoDescripcion = this.data.estado;
        this.AsuntoDescripcion = this.data.asunto;
        this.comentarios = this.data.comentarios;
        this.asuntoId = this.data.idAsunto;
        this.estadoId = this.data.idEstado;
    }

    public async selectEstado() {
        let agenda = new Agendas();
        let terr: any = await agenda.selectEstadoActividades();
        let listaestados = [];
        for (let t of terr)
            listaestados.push({description: t.descripcion, id: t.id});
        if (listaestados.length > 0) {
            this.selector.show({
                title: "SELECCIONAR ESTADO",
                items: [listaestados],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then((result: any) => {
                let resp: any = listaestados[result[0].index];
                this.estadoId = resp.id;
                this.EstadoDescripcion = resp.description;
            }, (err: any) => {
                console.log(err)
            });
        }
    }

    public async selectAsunto() {
        let agenda = new Agendas();
        let terr: any = await agenda.selectAsunto();
        let listasuntos = [];
        for (let t of terr)
            listasuntos.push({description: t.descripcion, id: t.id});
        if (listasuntos.length > 0) {
            this.selector.show({
                title: "SELECCIONAR ASUNTO",
                items: [listasuntos],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then((result: any) => {
                let resp: any = listasuntos[result[0].index];
                this.asuntoId = resp.id;
                this.AsuntoDescripcion = resp.description;
            }, (err: any) => {
                console.log(err)
            });
        }
    }

    public async Update() {
        try {
            let agenda = new Agendas();
            await agenda.updateRegister(this.id, this.estadoId, this.asuntoId, this.comentarios, agenda.getFechaPicker());
            this.toast.show('Se actualizó correctamente.', '3000', 'top').subscribe(toast => {
            });
            this.navCrl.navigateForward(`agendas`);
        } catch (e) {
            this.toast.show('ocurrió un error al momento de actualizar.', '3000', 'top').subscribe(toast => {
            });
        }
    }

    public async editar() {
        this.navCrl.navigateForward(`formagenda/` + this.data.cardCode + `/` + this.data.CardName + `/` + this.id);
    }
}