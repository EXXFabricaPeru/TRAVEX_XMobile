import { Component, OnInit } from '@angular/core';
import { Documentos } from "../../models/documentos";
import { ConfigService } from "../../models/config.service";
import { Pagos } from "../../models/pagos";
import { NavController } from "@ionic/angular";
import { NativeService } from "../../services/native.service";
import { ReportdiarioService } from "../../services/reportdiario.service"
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import * as moment from 'moment';
import { ReporteDiarioStyleoneService } from 'src/app/services/reporte-diario-styleone.service';

@Component({
    selector: 'app-informes',
    templateUrl: './informes.page.html',
    styleUrls: ['./informes.page.scss'],
})
export class InformesPage implements OnInit {
    public documentos: Documentos;
    public pagos: Pagos;
    public user: any;
    public fechaData: any;
    public fechamax: any;
    public path: any;
    public pathnew: any;
    public reportdiarioService;
    constructor(private configService: ConfigService, private spinnerDialog: SpinnerDialog, private _reportdiarioService: ReportdiarioService,
        private navCrl: NavController, public native: NativeService,private _reporteDiarioStyleOne:ReporteDiarioStyleoneService
    )
    {
        
        this.documentos = new Documentos();
        this.pagos = new Pagos();
        this.fechaData = this.documentos.getFechaPicker();
        this.fechamax = this.documentos.getFechaPicker();
        
    }

    async ngOnInit() {
        this.user = await this.configService.getSession();
        let styleLayaut = this.user[0].layautConfig ? this.user[0].layautConfig : null;
        console.log("  this.userdata 2 ", styleLayaut);
        
        switch (Number(styleLayaut))
        {
            case 0:
                this.reportdiarioService = this._reportdiarioService;
                break;
            case 1:
                console.log("  this.userdata 3 ", styleLayaut);
                this.reportdiarioService = this._reporteDiarioStyleOne;
                break;
            case 2:
            
                break;
            default:
                this.reportdiarioService = this._reportdiarioService;
                break;
        }
        this.path = await this.configService.getIp();
        const regex = /api/gi;
        this.pathnew = this.path.replace(regex, 'backend');
       
    }


    public async ventadiariaexxis() {
        let fecha: any = moment(this.fechaData).format('YYYY-MM-DD');
        let rx = await this.reportdiarioService.construcpdf(fecha, fecha);
        if (rx == true) this.reportdiarioService.generateEXE("reportediarioxmobile");
    }

    public async cierrediariaexxis() {
        let fecha: any = moment(this.fechaData).format('YYYY-MM-DD');
        let rx = await this.reportdiarioService.construcpdfCieereDiario(fecha, fecha);
        if (rx == true) this.reportdiarioService.generateEXE("reportediarioxmobile");
    }

    public async inventariosreport() {
        this.spinnerDialog.show('', 'Cargando...', true);
        let rx = await this.reportdiarioService.inventarioreport();
        if (rx == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobile");
        }
        this.spinnerDialog.hide();
    }


    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }

    async ResumenReport() {
        // this.spinnerDialog.show('', 'Cargando...', true);
        this.spinnerDialog.show('', 'Cargando...', true);
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenCaja = await this.reportdiarioService.reportResumenCaja(date);
        if (dataResumenCaja == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobile");
        }
        this.spinnerDialog.hide();
        // this.spinnerDialog.hide();
        

    }

    async detalleCajaReport() {
       
        this.spinnerDialog.show('', 'Cargando...', true);

        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenCaja = await this.reportdiarioService.reportResumenCajaDetalle(date);
        if (dataResumenCaja == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobile");
        }
        this.spinnerDialog.hide();

    }


    async detalleResumenVentas() {
       
        this.spinnerDialog.show('', 'Cargando...', true);
        
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenVentas = await this.reportdiarioService.reportResumenVentas(date);
        //  console.log("dataResumenVentas detalle ", dataResumenVentas);
        if (dataResumenVentas == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobile");
        }
        this.spinnerDialog.hide();
    }

    async detalleResumenVentasDetalle() {
        
        this.spinnerDialog.show('', 'Cargando...', true);
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenVentas = await this.reportdiarioService.selectAllResumenDocOfertas(date);
        //  console.log("dataResumenVentas detalle ", dataResumenVentas);
        if (dataResumenVentas == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobile");
        }
        this.spinnerDialog.hide();

    }

    async ResumenReportRollo() {
        // this.spinnerDialog.show('', 'Cargando...', true);
        this.spinnerDialog.show('', 'Cargando...', true);
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenCaja = await this.reportdiarioService.reportResumenCajaRollo(date);
        if (dataResumenCaja == true) {
            await this.reportdiarioService.generateEXE("reportediarioRolloxmobile");
        }
        this.spinnerDialog.hide();
        // this.spinnerDialog.hide();
        
    }

    async detalleCajaReportRollo() {
        this.spinnerDialog.show('', 'Cargando...', true);

        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenCaja = await this.reportdiarioService.reportResumenCajaDetalleRollo(date);
        if (dataResumenCaja == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobileRollo");
        }
        this.spinnerDialog.hide();
    }

    async detalleResumenVentasRollo() {
        
        this.spinnerDialog.show('', 'Cargando...', true);
        
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenVentas = await this.reportdiarioService.reportResumenVentasRollo(date);
        //  console.log("dataResumenVentas detalle ", dataResumenVentas);
        if (dataResumenVentas == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobileRollo");
        }
        this.spinnerDialog.hide();
    }


    async detalleResumenVentasDetalleRollo() {
        
        this.spinnerDialog.show('', 'Cargando...', true);
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let dataResumenVentas = await this.reportdiarioService.reportResumenVentasDetalleRollo(date);
        //  console.log("dataResumenVentas detalle ", dataResumenVentas);
        if (dataResumenVentas == true) {
            await this.reportdiarioService.generateEXE("reportediarioxmobileRollo");
        }
        this.spinnerDialog.hide();

    }

    async detalleBonificaciones() {
        // this.spinnerDialog.show('', 'Cargando...', true);
        let reportBonificaciones = await this.reportdiarioService.reportBonificaciones();
        //  console.log("dataResumenVentas detalle ", dataResumenVentas);
        if (reportBonificaciones == true) {
            await this.reportdiarioService.generateEXE("reportBonificacionesRollo");
        }
        // this.spinnerDialog.hide();

    }

    async cierredecaja() {

        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let reportCierrecaja = await this.reportdiarioService.cierrecaja(date);
        if (reportCierrecaja == true) {
            await this.reportdiarioService.generateEXE("reportCierredecaja");
        }
    }

    async detalledeinventario() {

        let reportCierrecaja = await this.reportdiarioService.detalledeinventario();
        if (reportCierrecaja == true) {
            await this.reportdiarioService.generateEXE("detalledeinventario");
        }
    }

    async ventasproductos() {
        let date: any = moment(this.fechaData).format('YYYY-MM-DD');
        let reportVentasproductos = await this.reportdiarioService.ventasproductos(date);
        if (reportVentasproductos == true) {
            await this.reportdiarioService.generateEXE("reportVentasproductos");
        }
    }

}
