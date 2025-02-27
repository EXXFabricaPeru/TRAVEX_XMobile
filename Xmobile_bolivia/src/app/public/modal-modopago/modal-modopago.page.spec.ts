import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { ModalModopagoPage } from './modal-modopago.page';

describe('ModalModopagoPage', () => {
  let component: ModalModopagoPage;
  let fixture: ComponentFixture<ModalModopagoPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalModopagoPage ],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(ModalModopagoPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
