import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalproductoPage } from './modalproducto.page';

describe('ModalproductoPage', () => {
  let component: ModalproductoPage;
  let fixture: ComponentFixture<ModalproductoPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalproductoPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ModalproductoPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
