void setup() {
  Serial.begin(9600);
  while(!Serial); // wait for the UART to be ready
  Serial.println("setup");
}

void loop() {
  char buffer[64]; int i;
  Serial.println("loop");
  for(i = 0; i < 63; i++)
    if(Serial.available()) buffer[i]=Serial.read();
    else break;
  buffer[i] = 0;
  if(i > 0) {
    Serial.print("received: ");
    Serial.println(buffer);
  }
  delay(1000);
}
