
import { Component,ViewChild, AfterContentInit } from '@angular/core';
import { Platform,ToastController,ModalController,NavParams,AlertController } from '@ionic/angular';
import { FirmaPage } from '../firma/firma.page';
import { Camera, CameraOptions, PictureSourceType } from '@ionic-native/camera/ngx';
import { Toast } from '@ionic-native/toast/ngx';
import { DataService } from "../../services/data.service";
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { ConfigService } from "../../models/config.service";
import { Documentos } from "../../models/documentos";
import { Geolocation } from '@ionic-native/geolocation/ngx';

const STORAGE_KEY = 'IMAGE_LIST';


@Component({
    selector: 'app-fromevidencia',
    templateUrl: './fromevidencia.page.html',
    styleUrls: ['./fromevidencia.page.scss'],
})

export class FromevidenciaPage{

    @ViewChild('firmaCanvas', { static: false }) canvas: any;

    public codDocumento: any;
    public data: any;
    public imagen: string;
    public currentImage: any = "";
    public Vevidencia1 = 'N';
    public data_imagenes: any = [];
    public canvasElement: any;
    public firmafinal: any;
    public limite_fotos = 0;
    public guarda = true;
    public formacargada = 0 ;

    constructor(private plt: Platform,public geolocation: Geolocation,  private configService: ConfigService, private spinnerDialog: SpinnerDialog, private dataService: DataService,private toast: Toast,private camera: Camera,private toastCtrl: ToastController, public alertController: AlertController,public modalController:ModalController,public navParams: NavParams) {
        this.codDocumento = 'sdsdasd';
        this.data = this.navParams.data;
        
    }
    
    public async ngOnInit(){
        this.imagen = "../../../assets/addPhoto.svg";
        for (let i = 0; i < 6; i++) {
            document.getElementById("foto"+i).style.display = "none";
            document.getElementById("Desfoto"+i).style.display = "none";
        }
        console.log(this.data);
        this.codDocumento = this.data.cod;
    }

    public closeModal(data: any) {
        this.modalController.dismiss(data);
    }

    public async Agregarfima(){

        try {
            let firmamodal: any = { component: FirmaPage,componentProps: "0"};
            let modalfirma: any = await this.modalController.create(firmamodal);

            modalfirma.onDidDismiss().then(async (data: any) => {
                console.log("datos retornados de la firma",data.data);
                this.guarda = false;
                var canvasPosition = this.canvasElement.getBoundingClientRect();
                let ctx = this.canvasElement.getContext('2d');
                ctx.clearRect(0, 0, canvasPosition.width, canvasPosition.height);
                // load image from data url
                var imageObj = new Image();
                imageObj.onload = function() {
                    ctx.drawImage(imageObj, 0, 0, canvasPosition.width, canvasPosition.height);
                };
                imageObj.src = data.data[0].imagen;
                this.firmafinal = data.data[0].imagen;
                this.formacargada = data.data[0].val;;
            });
            return await modalfirma.present();
        } catch (e) {
            console.log("error");
            console.log(e);
        }
    }

    public async eliminarfima(){
        var canvasPosition = this.canvasElement.getBoundingClientRect();
        let ctx = this.canvasElement.getContext('2d');
        ctx.clearRect(0, 0, canvasPosition.width, canvasPosition.height);
        this.firmafinal ='';
        this.formacargada = 0;
        if(this.data_imagenes.length == 0 && this.formacargada == 0){
            this.guarda = true;
        }
    }

    public async selectImage() {

        if(this.limite_fotos < 5){

        let alert: any = await this.alertController.create({
            header: 'OBTENER FOTO.',
            mode: 'ios',

            buttons: [{
                text: 'CAMARA',
                handler: () => {
                    this.takePicture(this.camera.PictureSourceType.CAMERA);

                }
            }, {
                text: 'GALERIA',
                handler: () => {
                    this.takePicture(this.camera.PictureSourceType.PHOTOLIBRARY);

                }
            }
                , {
                text: 'CANCELAR',
                handler: () => {


                }
            }
            ]
        });
        await alert.present();
    }else{
        this.toast.show("Ya se llego al limite maximo de evidencias", "4000", "top").subscribe(toast => {
        });
        return false;
    }

    }

    public async cargaimagenes(imagen){

        this.guarda = false;
        if(this.data_imagenes.length > 0){
            console.log("Ya tiene Datos");
            for await (let data of this.data_imagenes){
                console.log("recorre el objeto");
                this.limite_fotos = data.id;
            }

            console.log("el id es",this.limite_fotos);

            if(this.limite_fotos >= 5){
                console.log("limite de imagenes");
            }else{
                console.log("agrega el nuevo objeto");
                this.limite_fotos = this.limite_fotos+1
                this.data_imagenes.push({
                    id: this.limite_fotos,
                    imagen:imagen
                });

                document.getElementById("foto"+this.limite_fotos).style.display = "block";
                document.getElementById("Desfoto"+this.limite_fotos).style.display = "block";
                document.getElementById('foto'+this.limite_fotos).setAttribute( 'src', imagen);
            }
            

        }else{
            
            console.log("no tiene datos");
            this.data_imagenes.push({
                id: this.limite_fotos,
                imagen:imagen
            });
            document.getElementById("foto"+this.limite_fotos).style.display = "block";
                document.getElementById("Desfoto"+this.limite_fotos).style.display = "block";
                document.getElementById('foto'+this.limite_fotos).setAttribute( 'src', imagen);
        }

        console.log("El obejto sale",this.data_imagenes);

    }


    public async elimanaevidencia(id){

        for await (let data of this.data_imagenes){
            document.getElementById("foto"+data.id).style.display = "none";
            document.getElementById("Desfoto"+data.id).style.display = "none";
            document.getElementById('foto'+data.id).setAttribute( 'src',''); 
        }

        let aux: any = [];
        let id_aux = 0;
        for await (let data of this.data_imagenes){
            console.log("recorre el objeto");
            if(id != data.id){
                aux.push({
                    id: id_aux,
                    imagen:data.imagen
                });

                document.getElementById("foto"+id_aux).style.display = "block";
                document.getElementById("Desfoto"+id_aux).style.display = "block";
                document.getElementById('foto'+id_aux).setAttribute( 'src',data.imagen);
                this.limite_fotos = id_aux;
                id_aux ++;
            }
        }
        this.data_imagenes = aux;
        console.log("limite_fotos es",this.limite_fotos);

        if(this.data_imagenes.length == 0 && this.formacargada == 0){
            this.guarda = true;
        }

        console.log("el objeto final es",this.data_imagenes);

    }

    public async guardardatos(){
        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Guardar datos",
            message: "Está seguro de realizar esta acción? le recordamos que de confirmar no se podran enviar mas evidencias para este documento<strong></strong>",
            buttons: [
                {
                    text: "Cancelar",
                    role: "cancel",
                    cssClass: "secondary",
                    handler: (blah) => {
                        console.log("Confirm Cancel: blah");
                    },
                }, {
                    text: "Confirmar",
                    handler: async () => {
                        console.log("Confirm Okay");
                        await this.registreData();
                    },
                },
            ],
        });
        await alert.present();
    }

    public async registreData(){

        console.log(this.data);

        this.spinnerDialog.show(null, 'Enviando a SAP...', true);
        let datas: any = [];
        let detalle: any = [];
        let cabecera: any = [];
        let userdata = await this.configService.getSession();
        let Longitud = '';
        let latitud = '';

        await this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
            if (resp) {
                console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                console.log("registerLocation() ", "" + obj);
                localStorage.setItem("lat", obj.lat);
                localStorage.setItem("lng", obj.lng);
                Longitud = obj.lng;
                latitud = obj.lat;

            }
        }).catch(error => {
            console.log("Error ubicacion ---->", error);
            this.toast.show("Error al consultar Ubicacion actual", '3000', 'center').subscribe(toast => { });
            // return false;
        });

        detalle.push({
            firma:this.firmafinal,
            evidencia:this.data_imagenes
        });


        let today: any = new Date();
        let dd: any = today.getDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear();
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;

        let fecha = `${yyyy}-${mm}-${dd}`;

        cabecera.push({
            DocEntry: this.data.DocEntry,
            idDocPedido: this.data.DocNum,
            idUser: userdata[0].idUsuario,
            fechasend: fecha,
            U_LATITUD:latitud,
            U_LONGITUD:Longitud,
            CardCode:this.data.CardCode
        });
        datas.push({
            cabecera:cabecera[0],
            dealle:detalle[0]
        });

        console.log("se cargan los datos");
        console.log(datas);
        console.log(this.data);

        let respuesta = await this.dataService.exporevidencia(datas);
        console.log("RESPUESTA DEL MIDD: ",respuesta);

       

        let documentos = new Documentos();
        if(respuesta.estado = '3'){
            await documentos.updateEnvioEvidencia(this.data.DocNum,1);
        }else{
            await documentos.updateEnvioEvidencia(this.data.DocNum,0);
        }
        this.spinnerDialog.hide();
        this.toast.show(respuesta.mensaje, '3000', 'center').subscribe(toast => { });
        this.modalController.dismiss();
    }

    takePicture(sourceType) {
        const options: CameraOptions = {
            quality: 100,
            sourceType: sourceType,
            destinationType: this.camera.DestinationType.DATA_URL,
            encodingType: this.camera.EncodingType.JPEG,
            mediaType: this.camera.MediaType.PICTURE
        };

        this.camera.getPicture(options).then((imageData) => {
            this.currentImage = 'data:image/jpeg;base64,' + imageData;
            this.imagen = 'data:image/jpeg;base64,' + imageData;
            this.cargaimagenes(this.imagen);


        }, (err) => {
            this.currentImage = "";
            console.log("Camera issue:" + err);
        });
    }


    ngAfterViewInit() {
        this.canvasElement = this.canvas.nativeElement;
        this.canvasElement.width = this.plt.width() + '';
        this.canvasElement.height = 150;
    }
}
