
import { Component,ViewChild, AfterContentInit } from '@angular/core';
import { Platform,ToastController,ModalController } from '@ionic/angular';



const STORAGE_KEY = 'IMAGE_LIST';


@Component({
    selector: 'app-firma',
    templateUrl: './firma.page.html',
    styleUrls: ['./firma.page.scss'],
})

export class FirmaPage{

        @ViewChild('imageCanvas', { static: false }) canvas: any;

        
        canvasElement: any;
        saveX: number;
        saveY: number;
      

        drawing = false;
        lineWidth = 10;
        file: any;
      
        constructor(private plt: Platform,  private toastCtrl: ToastController, public modalController:ModalController) {}


        public closeModal(data: any) {
            this.modalController.dismiss(data);
        }

        ngAfterViewInit() {
          
          this.canvasElement = this.canvas.nativeElement;
          this.canvasElement.width = this.plt.width() + '';
          this.canvasElement.height = 600;
        }
      
        startDrawing(ev) {
          this.drawing = true;
          var canvasPosition = this.canvasElement.getBoundingClientRect();
            if(ev.pageX){
                this.saveX = parseInt(ev.pageX) - canvasPosition.x;
                this.saveY = parseInt(ev.pageY) - canvasPosition.y;
            }else{
                this.saveX = parseInt(ev.targetTouches[0].pageX) - canvasPosition.x;
                this.saveY = parseInt(ev.targetTouches[0].pageY) - canvasPosition.y;
            }
        }
      
        endDrawing() {
          this.drawing = false;
        }
      
        moved(ev) {

            if (!this.drawing) return;
          
            var canvasPosition = this.canvasElement.getBoundingClientRect();
            let ctx = this.canvasElement.getContext('2d');

            let currentX = 0;
            let currentY = 0;

            if(ev.pageX){
                 currentX = parseInt(ev.pageX) - canvasPosition.x;
                 currentY = parseInt(ev.pageY) - canvasPosition.y;
            }else{
                 currentX = parseInt(ev.targetTouches[0].pageX) - canvasPosition.x;
                 currentY = parseInt(ev.targetTouches[0].pageY) - canvasPosition.y;
            }
            
          
            ctx.beginPath();
            ctx.moveTo(this.saveX, this.saveY);
            ctx.lineTo(currentX, currentY);
            ctx.closePath();
            ctx.stroke();

            
            this.saveX = currentX;
            this.saveY = currentY;
        }

        delete(){

            var canvasPosition = this.canvasElement.getBoundingClientRect();
            let ctx = this.canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasPosition.width, canvasPosition.height);
            
        }

        saveCanvasImage() {
            var dataUrl = this.canvasElement.toDataURL();
            let aux: any = [];
            aux.push({
                val: 1,
                imagen:dataUrl
            });
            
            this.closeModal(aux);
          }

    

}
