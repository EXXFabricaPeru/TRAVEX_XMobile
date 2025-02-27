import { Component, OnInit, ViewChild } from '@angular/core';
import { ConfigService } from "../../models/config.service";
import { Clientes } from "../../models/clientes";
import { NavController, IonInfiniteScroll, MenuController } from "@ionic/angular";
import { Toast } from "@ionic-native/toast/ngx";
import { ActivatedRoute } from '@angular/router';
import { DataService } from '../../services/data.service';
import { Documentos } from '../../models/documentos';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import {GlobalConstants} from '../../../global'

@Component({
    selector: 'app-clientes',
    templateUrl: './clientes.page.html',
    styleUrls: ['./clientes.page.scss'],
})
export class ClientesPage implements OnInit {
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
    private clienteData: Clientes;
    private lmt: number;
    public userdata: any;

    constructor(private configService: ConfigService, private navCrl: NavController, private toast: Toast, private spinnerDialog: SpinnerDialog,
        private activatedRoute: ActivatedRoute, private menu: MenuController, private dataService: DataService) {
        this.oringen = "";
        this.searchData = "";
        this.lmt = 0;
        this.items = [];
        this.loadItem = false;
        this.conexion = false;
        this.xcc = true;
        this.titulo = '';
        this.textLoad = 'Cargando...';
        this.clienteData = new Clientes();
    }

    public async crearCliente() {
        console.log("this.userdata[0] ", this.userdata[0]);
        if (this.userdata[0].config[0].permisoCrearClientes == '0') {
            this.toast.show(`No está permitido para crear clientes.`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        this.navCrl.navigateForward(`formcliente/null`);
    }

    public async ngOnInit() {
        this.menu.enable(true, 'menuxmobile');
        this.menu.close('menuxmobile');
        this.userdata = await this.configService.getSession();
        localStorage.setItem("VistaCliNormal", "1");
    }

    private async ionViewWillEnter() {
        this.lmt = 0;
        this.items = [];
        this.oringen = this.activatedRoute.snapshot.paramMap.get("id");
        let id: any = await this.configService.getSession();
        this.id = id.idUsuario;
        this.dataClientes();
        (this.oringen == 'all' || this.oringen == 'age') ? this.titulo = 'Clientes' : this.titulo = 'Deudas de clientes';
    }

    public searchInput(event: any) {
        this.lmt = 0;
        this.items = [];
        (event.detail.value != '') ? this.searchData = event.detail.value : this.searchData = '';
        this.dataClientes();
    }

    public async dataClientes() {
        try {
            let arritems: any = await this.clienteData.findAllCliente(this.lmt, this.searchData, this.oringen);
            console.log("arritems ", arritems);

            this.toast.show(`Son ${ arritems.length } clientes encontrados`, '2500', 'center').subscribe(toast => {
            });

            let data = arritems;

            let result = data.filter((item, index) => {
                return data.indexOf(item) === index;
            })
            console.log("filtrado arritems ", result); //[1,2,6,5,9,'33']

            for (let item of result) this.items.push(item);
            (this.items.length > 0) ? this.textLoad = '' : this.textLoad = 'Sin resultados';
        } catch (error) {
            this.toast.show(error, '2500', 'center').subscribe(toast => {
            });
        }        
    }

    public loadData(event) {
        setTimeout(() => {
            this.lmt += 20;
            this.dataClientes();
            event.target.complete();
        }, 500);
    }

    public async findItem(item: any) {
        console.log("itemm ", item)
        this.spinnerDialog.show();
        let clientes: any = new Clientes();
        let resp: any = await this.dataService.getClientesAction(item.CardCode);
        if(resp != 0){
            if (resp.error && resp.error == 201) {
                console.log("ERROR AL BUSCAR DEUDA DEL CLIENTE");
            }else{
                console.log("DATOS CLIENTES",resp[0]);
                await clientes.updateDataSapClient(resp);
            }
        }else{
            this.toast.show(`Modo Offline no se puede dascargar deuda.`, '2500', 'center').subscribe(toast => {
            });
        }
        this.spinnerDialog.hide();


        if (this.oringen == 'all') this.navCrl.navigateForward(`cliente/` + item.CardCode);
        else if (this.oringen == 'age') this.navCrl.navigateForward(`formagenda/` + item.CardCode + `/` + item.CardName + `/NULL`); //alcides
        else this.navCrl.navigateForward(`pendientes/` + item.CardCode);
    }

    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }
}
