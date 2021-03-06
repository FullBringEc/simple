/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.sofis.simple;

import java.io.IOException;
import java.nio.charset.Charset;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.logging.Level;
import java.util.logging.Logger;
import junit.framework.TestCase;
import org.bouncycastle.util.encoders.Base64;
import org.junit.Test;

/**
 *
 * @author bruno
 */

public class XMLSignerTestValidar extends TestCase {

    public XMLSignerTestValidar(String testName) {
        super(testName);
    }

    @Override
    protected void setUp() throws Exception {
        super.setUp();
    }

    @Override
    protected void tearDown() throws Exception {
        super.tearDown();
    }

    /**
     * Test of main method, of class XMLSigner.
     */
    @Test
    public void testMain() {
        
        try {
            System.out.println("main");
            String[] args = new String[3];
                
            String docu = this.readFile("/home/spio/Desktop/simple1/SAE-EjemploRespuesta.xml", Charset.defaultCharset());
            docu = new String(Base64.encode(docu.getBytes()));
            String cert = "CDA-Publico-Testing2.cer";
            
            args[0] = "validar";
            args[1] = cert;
            args[2] = docu;
            
            System.out.println("###############################################################################");
            System.out.println("CALL: java -jar signer.jar " + args[0]+" " + args[1] +" "+ args[2]);
            System.out.println("###############################################################################");
            
            
            XMLSigner.main(args);
            
        } catch (IOException ex) {
            Logger.getLogger(XMLSignerTestValidar.class.getName()).log(Level.SEVERE, null, ex);
        }

    }

    public String readFile(String path, Charset encoding) throws IOException {
        byte[] encoded = Files.readAllBytes(Paths.get(path));
        return new String(encoded, encoding);
    }

}
