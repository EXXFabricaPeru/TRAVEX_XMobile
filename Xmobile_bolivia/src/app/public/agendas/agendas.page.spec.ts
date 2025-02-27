import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AgendasPage } from './agendas.page';

describe('AgendasPage', () => {
  let component: AgendasPage;
  let fixture: ComponentFixture<AgendasPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AgendasPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AgendasPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
