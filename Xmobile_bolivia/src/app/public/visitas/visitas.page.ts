import { Component, OnInit } from '@angular/core';
import { AlertController, ModalController, NavParams } from "@ionic/angular";
import { Visitas } from "../../models/visitas";
import { Camera, CameraOptions, PictureSourceType } from '@ionic-native/camera/ngx';
import { Toast } from "@ionic-native/toast/ngx";
import { File } from '@ionic-native/file/ngx';
import { WebView } from "@ionic-native/ionic-webview/ngx";
import { DataService } from "../../services/data.service";

@Component({
    selector: 'app-visitas',
    templateUrl: './visitas.page.html',
    styleUrls: ['./visitas.page.scss'],
})
export class VisitasPage implements OnInit {
    public images = [{
        name: 'addPhoto.svg',
        path: '',
        filePath: '../../assets/addPhoto.svg'
    }];
    public data: any;
    public id: any;
    public selectedImage: any;
    public imagen: string;
    public win: any = window;
    public items: any;
    public estadovisita: boolean;
    currentImage: any;
    motivosNovisitaData: any;
    motivo: any = {};

    public motivoDesc: string = "";

    constructor(public modalController: ModalController, public navParams: NavParams, private dataService: DataService,
        private camera: Camera, private file: File, public alertController: AlertController, private toast: Toast, private webview: WebView) {
        this.data = this.navParams.data;
        this.items = [];

        this.estadovisita = true;
    }

    ngOnInit() {

        this.motivosNovisitaData = JSON.parse(localStorage.getItem("motivosNoVenta"));
        //this.imagen = "../../../assets/addPhoto.svg";
        console.log(" JSON.parse(localStorage.getItem motivosNoVenta ", JSON.parse(localStorage.getItem("motivosNoVenta")));

        if(!this.motivosNovisitaData){
            this.motivosNovisitaData = [{ Razon: 'Sin Razón' },{ Razon: 'Tienda cerrada' },{ Razon: 'Tienda sin stock' }];
        }

        this.listardata();
    }
    public async ionViewWillEnter() {
        this.imagen = "../../../assets/addPhoto.svg";
    }
    public async listardata() {
        let visitas = new Visitas();
        
        this.items = await visitas.listdata(this.data.CardCode);
        
        console.log("this.items ", this.items);
    }

    public async registrar() {
        this.estadovisita = false;
        let visitas = new Visitas();
        this.id = await visitas.insert(this.data, this.currentImage);
    }

    public async finalizarregister() {
        this.estadovisita = true;
        this.update();
        this.dataService.exportVistas();
        this.listardata();
    }
    async noVisit() {
        if (this.motivoDesc.trim() == "" || this.isObjEmpty(this.motivo)) {


            return this.toast.show("Completa los campos.", "3000", "top").subscribe(toast => {
            });
        }
        //   console.log("motivoDesc ", this.motivoDesc);
        this.data.motivoCode = this.motivo.Code;
        this.data.motivoRazon = this.motivo.Razon;
        this.data.motivoName = this.motivo.Name;
        this.data.descripcionTxt = this.motivoDesc;
        // this.estadovisita = false;
        let visitas = new Visitas();
        console.log("this.data ", this.data);
        /**
         * CardCode: "1004100017"
        CardName: "ANA CONDE"
        Name: "PRUEBA2"
        Razon: "DIRECCION INCORRECTA"
        foto: "null.jpg"
        lat: -16.4896983
        lng: -68.1192983
        motivoCode: "COD0002"
        motivoDesc: "dfgd"
         */
        if (!this.currentImage) {
            this.currentImage = this.imagen;
        }
        console.log("this.currentImage ", this.currentImage);
        this.id = await visitas.insert(this.data, this.currentImage);
        this.motivosNovisitaData = [];
        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: "Confirmar ",
            message: "¿Está seguro de guardar el registro?. <strong> </strong>",
            buttons: [
                {
                    text: "Cancelar",
                    role: "cancel",
                    cssClass: "secondary",
                    handler: (blah) => {
                        console.log("Confirm Cancel: blah");
                        return false;
                    },
                },
                {
                    text: "Aceptar",
                    //  handler: () => {
                    handler: async (data: any) => {
                        console.log("---> continuar ");
                        this.toast.show("Registro guardado exitosamente.", "4000", "top").subscribe(toast => {
                        });
                        this.modalController.dismiss(true);
                        this.listardata();

                    },
                },
            ],
        });

        await alert.present();

        //this.motivosNovisitaData = JSON.parse(localStorage.getItem("motivosNoVenta"));
        // this.motivo = {};
        //  this.modalController.dismiss(true);
    }

    isObjEmpty(obj) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop)) return false;
        }

        return true;
    }

    onChange($event) {
        console.log($event.target.value);
        let value = $event.target.value;
        this.motivo = value;
    }
    public async selectImage() {
        this.takePicture(this.camera.PictureSourceType.CAMERA);
    }

    public createFileName() {
        return new Promise((resolve, reject) => {
            resolve(Date.now() + ".jpg");
        });
    }

    public async takePicture(sourceType: PictureSourceType) {
        // let fileNameArchivo: any = await this.createFileName();
        try {
            /*
                        let options: CameraOptions = {
                            quality: 50,
                            destinationType: this.camera.DestinationType.FILE_URI,
                            sourceType: sourceType,
                            saveToPhotoAlbum: true,
                            correctOrientation: true,
                            mediaType: this.camera.MediaType.PICTURE,
                            targetWidth: 250,
                            targetHeight: 250
                        };
                        let imagePath: any = await this.camera.getPicture(options);
                        */

            let options2: CameraOptions = {
                quality: 50,
                sourceType: sourceType,
                saveToPhotoAlbum: true,
                correctOrientation: true,
                destinationType: this.camera.DestinationType.DATA_URL,
                encodingType: this.camera.EncodingType.JPEG,
                mediaType: this.camera.MediaType.PICTURE,
                targetWidth: 250,
                targetHeight: 250
            };

            this.camera.getPicture(options2).then((imageData) => {
                this.currentImage = 'data:image/jpeg;base64,' + imageData;
                this.imagen = this.currentImage;
                //   this.imagen = 'data:image/jpeg;base64,' + imageData;
                console.log("this.currentImage ", this.currentImage);
            }, (err) => {
                this.currentImage = "";
                // Handle error
                console.log("Camera issue:" + err);
            });


            //this.currentImage = 'data:image/jpeg;base64,' + imagePath;

            console.log("this.currentImage ", this.currentImage);


            // let currentName = imagePath.substr(imagePath.lastIndexOf("/") + 1);
            // let correctPath = imagePath.substr(0, imagePath.lastIndexOf("/") + 1);
            // this.copyFileToLocalDir(correctPath, currentName, fileNameArchivo);
            // this.updateFoto(imagePath);
        } catch (e) {
            this.toast.show("La imagen no pudo ser cargada por la tipo por la versión del dispositivo", "3000", "top").subscribe(toast => {
            });
        }
    }

    public copyFileToLocalDir(namePath, currentName, newFileName) {
        this.file.copyFile(namePath, currentName, this.file.externalApplicationStorageDirectory, newFileName).then(() => {
            this.updateStoredImages(newFileName);
        }, error => {
            this.toast.show("La imagen no pudo ser cargada por la tipo por la versión del dispositivo.", "3000", "top").subscribe(toast => {
            });
        });
    }

    public async updateStoredImages(name) {
        let pathExternal = this.file.externalApplicationStorageDirectory;
        this.imagen = this.webview.convertFileSrc(pathExternal + name);
        let filePath = this.file.externalApplicationStorageDirectory + name;
        let resPath = this.pathForImage(filePath);
        let newEntry = {
            name: name,
            path: resPath,
            filePath: filePath
        };
        this.images = [newEntry];
        this.selectedImage = await this.win.Ionic.WebView.convertFileSrc(filePath);
        this.imagen = this.selectedImage;
    }

    public pathForImage(img) {
        if (img === null) {
            return '';
        } else {
            let converted = this.webview.convertFileSrc(img);
            return converted;
        }
    }

    public async update() {
        if (typeof this.id != 'undefined') {
            let visitas = new Visitas();
            await visitas.updateHora(this.id);
        }
    }

    public async updateFoto(imagePath: string) {
        let visitas = new Visitas();
        await visitas.updateFoto(this.id, imagePath);
    }

    public cerrar(data: any) {
        this.update();
        this.modalController.dismiss(false);
    }
}
