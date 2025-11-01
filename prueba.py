#num = float(input("Ingresa un nuemro"))
num1= float(input(" Ingrese un numero"))
num2= float(input ("Ingrese otro numero"))
num3= float(input("Ingrese otro numero"))
promedio = (num1+num2+ num3)/3
if promedio < 6:
    print("desaprobado ")
elif promedio >=7 and promedio<8:
    print ("estandar")
elif promedio >=8 and promedio<9:
    print("destacado")
elif promedio >=9 and promedio<=10:
    print("Excelente")
