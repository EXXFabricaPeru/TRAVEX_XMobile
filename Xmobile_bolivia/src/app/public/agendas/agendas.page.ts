import {Component, OnInit, ViewChild} from '@angular/core';
import {ConfigService} from "../../models/config.service";
import {Agendas} from "../../models/agendas";
import {NavController, IonInfiniteScroll} from "@ionic/angular";
import {Toast} from "@ionic-native/toast/ngx";
import {ActivatedRoute} from '@angular/router';

@Component({
    selector: 'app-agendas',
    templateUrl: './agendas.page.html',
    styleUrls: ['./agendas.page.scss'],
})
export class AgendasPage implements OnInit {
    @ViewChild(IonInfiniteScroll) infiniteScroll: IonInfiniteScroll;
    public id: number;
    public items: any;
    public loadItem: boolean;
    public conexion: boolean;
    public xcc: boolean;
    public oringen: string;
    public titulo: string;
    public textLoad: string;
    public imagen: string;
    public searchData: string;
    private agendaData: Agendas;
    private lmt: number;

    constructor(private configService: ConfigService, private navCrl: NavController, private toast: Toast,
                private activatedRoute: ActivatedRoute) {
        this.oringen = "";
        this.searchData = "";
        this.lmt = 0;
        this.items = [];
        this.loadItem = false;
        this.conexion = false;
        this.xcc = true;
        this.titulo = 'Agenda';
        this.textLoad = 'Cargando...';
        this.agendaData = new Agendas();
    }

    public async ngOnInit() {
    }

    private async ionViewWillEnter() {
        this.oringen = this.activatedRoute.snapshot.paramMap.get("id");
        let id: any = await this.configService.getSession();
        this.id = id.idUsuario;
        this.dataAgendas();
    }

    public async dataAgendas() {
        let data: any = await this.agendaData.findAll(this.lmt, this.searchData);
        for (let itm = 0; itm < data.rows.length; itm++)
            this.items.push(data.rows.item(itm));
        (this.items.length > 0) ? this.textLoad = '' : this.textLoad = 'Sin resultados';
    }

    public searchInput(event: any) {
        this.lmt = 0;
        this.items = [];
        (event.detail.value != '') ? this.searchData = event.detail.value : this.searchData = '';
        this.dataAgendas();
    }

    public loadData(event) {
        setTimeout(() => {
            this.lmt += 20;
            this.dataAgendas();
            event.target.complete();
        }, 500);
    }

    public crearActividad() {
        this.navCrl.navigateForward(`clientes/age`);
    }

    public findItem(item: any) {
        this.navCrl.navigateForward(`agenda/` + item.id);
    }

    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }

}
